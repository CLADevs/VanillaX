<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\blocks\tile\CommandBlockTile;
use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use CLADevs\VanillaX\entities\utils\interfaces\EntityInteractable;
use CLADevs\VanillaX\entities\utils\interfaces\EntityRidable;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use CLADevs\VanillaX\inventories\InventoryManager;
use CLADevs\VanillaX\inventories\types\TradeInventory;
use CLADevs\VanillaX\listeners\ListenerManager;
use CLADevs\VanillaX\utils\instances\InteractButtonResult;
use CLADevs\VanillaX\utils\item\InteractButtonItemTrait;
use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\java\GameModeIdMap;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBlockPickEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\ActorPickRequestPacket;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\CraftingDataPacket;
use pocketmine\network\mcpe\protocol\EmotePacket;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\SetDefaultGameTypePacket;
use pocketmine\network\mcpe\protocol\SetDifficultyPacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData;
use pocketmine\network\mcpe\protocol\types\recipe\PotionContainerChangeRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\PotionTypeRecipe;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

class PacketListener implements Listener{

    private ListenerManager $manager;

    public function __construct(ListenerManager $manager){
        $this->manager = $manager;
    }

    public function onInventoryTransaction(InventoryTransactionEvent $event): void{
        VanillaX::getInstance()->getEnchantmentManager()->handleInventoryTransaction($event);
    }

    public function onDataPacketSend(DataPacketSendEvent $event): void{
        if(!$event->isCancelled()){
            foreach($event->getPackets() as $packet){
                switch($packet::NETWORK_ID){
                    case ProtocolInfo::AVAILABLE_COMMANDS_PACKET:
                        if($packet instanceof AvailableCommandsPacket) $this->handleCommandEnum($packet);
                        break;
                    case ProtocolInfo::CRAFTING_DATA_PACKET:
                        if($packet instanceof CraftingDataPacket) $this->handleCraftingData($packet);
                        break;
                }
            }
        }
    }

    public function onDataPacketReceive(DataPacketReceiveEvent $event): void{
        if(!$event->isCancelled() && ($player = $event->getOrigin()->getPlayer()) !== null){
            $packet = $event->getPacket();
            $sessionManager = VanillaX::getInstance()->getSessionManager();
            $session = $sessionManager->has($player) ? $sessionManager->get($player) : null;
            $window = $player->getCurrentWindow();

            if($window instanceof FakeBlockInventory && !$window->handlePacket($player, $packet)){
                $event->cancel();
                return;
            }
            switch($packet::NETWORK_ID){
                case ProtocolInfo::COMMAND_BLOCK_UPDATE_PACKET:
                    if($packet instanceof CommandBlockUpdatePacket && $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)) $this->handleCommandBlock($player, $packet);
                    break;
                case ProtocolInfo::PLAYER_ACTION_PACKET:
                    if($packet instanceof PlayerActionPacket && in_array($packet->action, [PlayerActionPacket::ACTION_START_GLIDE, PlayerActionPacket::ACTION_STOP_GLIDE])){
                        $session->setGliding($packet->action === PlayerActionPacket::ACTION_START_GLIDE);
                    }
                    break;
                case ProtocolInfo::INVENTORY_TRANSACTION_PACKET:
                    if($packet instanceof InventoryTransactionPacket) $this->handleInventoryTransaction($player, $packet);
                    break;
                case ProtocolInfo::SET_PLAYER_GAME_TYPE_PACKET:
                    /** Server Form Personal Game Type Setting */
                    if($player->hasPermission(DefaultPermissions::ROOT_OPERATOR) && $packet instanceof SetPlayerGameTypePacket){
                        $player->setGamemode(GameModeIdMap::getInstance()->fromId($packet->gamemode));
                    }
                    break;
                case ProtocolInfo::SET_DEFAULT_GAME_TYPE_PACKET:
                    /** Server Form Default Game Type Setting */
                    if($player->hasPermission(DefaultPermissions::ROOT_OPERATOR) && $packet instanceof SetDefaultGameTypePacket){
                        Server::getInstance()->getConfigGroup()->setConfigInt("gamemode", $packet->gamemode);
                    }
                    break;
                case ProtocolInfo::SET_DIFFICULTY_PACKET:
                    /** Server Form Difficulty Setting */
                    if($player->hasPermission(DefaultPermissions::ROOT_OPERATOR) && $packet instanceof SetDifficultyPacket){
                        $player->getWorld()->setDifficulty($packet->difficulty);
                    }
                    break;
                case ProtocolInfo::CONTAINER_CLOSE_PACKET:
                    /** Fixes Trading GUI issue */
                    if($packet instanceof ContainerClosePacket && $packet->windowId === 255){
                        $inv = $player->getCurrentWindow();

                        if($inv instanceof TradeInventory){
                            $player->removeCurrentWindow();
                        }
                    }
                    break;
                case ProtocolInfo::INTERACT_PACKET:
                    if($packet instanceof InteractPacket) $this->handleInteract($player, $packet);
                    break;
                case ProtocolInfo::ACTOR_PICK_REQUEST_PACKET:
                    if($packet instanceof ActorPickRequestPacket) $this->handleActorPickRequest($player, $packet);
                    break;
                case ProtocolInfo::EMOTE_PACKET:
                    if($packet instanceof EmotePacket) $this->handleEmote($player, $packet);
                    break;
            }
        }
    }

    /**
     * @param AvailableCommandsPacket $packet
     * Modifies enums for commands, arguments you see once you type /weather
     * are modified through this packet. '<clear: rain: thunder> [duration: int]'
     */
    private function handleCommandEnum(AvailableCommandsPacket $packet): void{
        foreach(VanillaX::getInstance()->getCommandManager()->getCommands() as $key => $command){
            if(($arg = $command->getCommandArg()) !== null && ($command = $packet->commandData[strtolower($key)] ?? null) !== null){
                $command->flags = $arg->getFlags();
                $command->permission = $arg->getPermission();
                $command->overloads = $arg->getOverload();
            }
        }
    }

    /**
     * @param Player $player
     * @param CommandBlockUpdatePacket $packet
     * Changes server sided command block tile data
     */
    private function handleCommandBlock(Player $player, CommandBlockUpdatePacket $packet): void{
        $position = new Position($packet->x, $packet->y, $packet->z, $player->getWorld());
        $tile = $position->getWorld()->getTile($position);

        if($tile instanceof CommandBlockTile){
            $tile->handleCommandBlockUpdateReceive($packet);
        }
    }

    /**
     * @param Player $player
     * @param InventoryTransactionPacket $packet
     * This is for interacting with villagers for trading, changing armor stand armor, etc.
     */
    private function handleInventoryTransaction(Player $player, InventoryTransactionPacket $packet): void{
        if($packet->trData instanceof UseItemOnEntityTransactionData && $packet->trData->getActionType() === UseItemOnEntityTransactionData::ACTION_INTERACT){
            $entity = $player->getWorld()->getEntity($packet->trData->getEntityRuntimeId());
            $item = TypeConverter::getInstance()->netItemStackToCore($packet->trData->getItemInHand()->getItemStack());
            $currentButton = VanillaX::getInstance()->getSessionManager()->get($player)->getInteractiveText();
            $clickPos = $packet->trData->getClickPos();
            $button = null;

            if(is_string($currentButton) && count($packet->trData->getActions()) < 1){
                if($entity instanceof InteractButtonItemTrait){
                    /** Whenever a player interacts with interactable button for entity */
                    $entity->onButtonPressed($button = new InteractButtonResult($player, $item, $currentButton, $clickPos));
                }
                if($item instanceof InteractButtonItemTrait){
                    /** Whenever a player interacts with interactable button for item */
                    $item->onButtonPressed($button = new InteractButtonResult($player, $item, $currentButton, $clickPos));
                }
            }

            if($entity instanceof EntityInteractable){
                /** If a player interacts with entity with a item */
                if($button === null || $button->canInteractQueue()){
                    $entity->onInteract(new EntityInteractResult($player, $item, null, $clickPos, $currentButton));
                }
            }
            if($item instanceof EntityInteractable){
                /** If a player interacts with entity with a item that has EntityInteractable trait */
                $item->onInteract(new EntityInteractResult($player, null, $entity));
            }
        }elseif($packet->trData instanceof UseItemTransactionData && $packet->trData->getActionType() === UseItemTransactionData::ACTION_CLICK_AIR){
            $entity = VanillaX::getInstance()->getSessionManager()->get($player)->getRidingEntity();
            $item = TypeConverter::getInstance()->netItemStackToCore($packet->trData->getItemInHand()->getItemStack());
            $currentButton = VanillaX::getInstance()->getSessionManager()->get($player)->getInteractiveText();

            if(is_string($currentButton) && count($packet->trData->getActions()) < 1){
                if($entity instanceof InteractButtonItemTrait){
                    /** Whenever a player interacts with interactable button for entity */
                    $entity->onButtonPressed($button = new InteractButtonResult($player, $item, $currentButton));
                }
                if($item instanceof InteractButtonItemTrait){
                    /** Whenever a player interacts with interactable button for item */
                    $item->onButtonPressed($button = new InteractButtonResult($player, $item, $currentButton));
                }
            }
        }
    }

    /**
     * @param Player $player
     * @param InteractPacket $packet
     * This handles button once you hover over entities
     * or once you leave your ride
     */
    private function handleInteract(Player $player, InteractPacket $packet): void{
        $entity = $player->getWorld()->getEntity($packet->target);
        $session = VanillaX::getInstance()->getSessionManager()->get($player);

        if($packet->action === InteractPacket::ACTION_MOUSEOVER){
            if($packet->target == 0 && $packet->x == 0 && $packet->y == 0 && $packet->z == 0){
                $entity = $session->getRidingEntity();

                if($entity === null){
                    $player->getNetworkProperties()->setString(EntityMetadataProperties::INTERACTIVE_TAG, "");
                }
            }elseif($entity instanceof InteractButtonItemTrait){
                $entity->onMouseHover($player);
            }
        }elseif($packet->action === InteractPacket::ACTION_LEAVE_VEHICLE && $entity instanceof EntityRidable){
            $entity->onLeftRide($player);
        }
        /** fixes not being able to open inventory while riding entities */
        if($packet->action === InteractPacket::ACTION_OPEN_INVENTORY && ($entity = $session->getRidingEntity()) !== null){
            $packet->target = $session->getEntityId();
        }
    }

    /**
     * @param Player $player
     * @param ActorPickRequestPacket $packet
     * This is called whenever you middle click on an entity
     */
    private function handleActorPickRequest(Player $player, ActorPickRequestPacket $packet): void{
        $entity = $player->getWorld()->getEntity($packet->entityUniqueId);

        if($entity instanceof Entity && !$entity instanceof Human){
            $result = ItemFactory::getInstance()->get(ItemIds::SPAWN_EGG, $entity::NETWORK_ID);
            $ev = new PlayerBlockPickEvent($player, VanillaBlocks::AIR(), $result);
            $ev->call();

            if(!$ev->isCancelled()){
                $player->getInventory()->setItemInHand($ev->getResultItem());
            }
        }
    }

    /**
     * @param Player $player
     * @param EmotePacket $packet
     * This is called whenever player emotes
     */
    private function handleEmote(Player $player, EmotePacket $packet): void{
        foreach($player->getViewers() as $viewer){
            $viewer->getNetworkSession()->sendDataPacket($packet);
        }
    }

    /**
     * @param CraftingDataPacket $packet
     * called whenever player joins to send recipes for brewing, crafting, etc
     */
    private function handleCraftingData(CraftingDataPacket $packet): void{
        $potionTypeRecipes = [];
        foreach(json_decode(file_get_contents(Utils::getResourceFile("brewing_recipes.json")), true) as $key => $i){
            $packet->potionTypeRecipes[] = new PotionTypeRecipe($i[0], $i[1], $i[2], $i[3], $i[4], $i[5]);
            $potion = new PotionTypeRecipe(InventoryManager::convertPotionId($i[0]), InventoryManager::convertPotionId($i[1]), InventoryManager::convertPotionId($i[2]), InventoryManager::convertPotionId($i[3]), InventoryManager::convertPotionId($i[4]), InventoryManager::convertPotionId($i[5]));
            $potionTypeRecipes[$potion->getInputItemId() . ":" . $potion->getInputItemMeta() . ":" . $potion->getIngredientItemId() . ":" . $potion->getIngredientItemMeta()] = clone $potion;
        }

        $potionContainerRecipes = [];
        foreach([[426, 328, 561], [561, 560, 562]] as $key => $i){
            $packet->potionContainerRecipes[] = new PotionContainerChangeRecipe($i[0], $i[1], $i[2]);
            $potion = new PotionContainerChangeRecipe(InventoryManager::convertPotionId($i[0]), InventoryManager::convertPotionId($i[1]), InventoryManager::convertPotionId($i[2]));
            $potionContainerRecipes[$potion->getInputItemId() . ":" . $potion->getIngredientItemId()] = clone $potion;
        }
        InventoryManager::getInstance()->setPotionTypeRecipes($potionTypeRecipes);
        InventoryManager::getInstance()->setPotionContainerRecipes($potionContainerRecipes);
    }
}