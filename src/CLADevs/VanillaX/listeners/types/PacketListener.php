<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\blocks\tile\CommandBlockTile;
use CLADevs\VanillaX\entities\EntityManager;
use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use CLADevs\VanillaX\entities\utils\interfaces\EntityInteractable;
use CLADevs\VanillaX\entities\utils\interfaces\EntityRidable;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\event\player\PlayerEntityPickEvent;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use CLADevs\VanillaX\inventories\InventoryManager;
use CLADevs\VanillaX\inventories\types\TradeInventory;
use CLADevs\VanillaX\session\Session;
use CLADevs\VanillaX\utils\instances\InteractButtonResult;
use CLADevs\VanillaX\utils\item\InteractButtonItemTrait;
use CLADevs\VanillaX\VanillaX;
use pocketmine\data\java\GameModeIdMap;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\convert\ItemTranslator;
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
use pocketmine\network\mcpe\protocol\types\PlayerAction;
use pocketmine\network\mcpe\protocol\types\recipe\PotionContainerChangeRecipe;
use pocketmine\network\mcpe\protocol\types\recipe\PotionTypeRecipe;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use const pocketmine\BEDROCK_DATA_PATH;

class PacketListener implements Listener{

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
                    if($packet instanceof PlayerActionPacket) $this->handlePlayerAction($session, $packet);
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
        $position = new Position($packet->blockPosition->getX(), $packet->blockPosition->getY(), $packet->blockPosition->getZ(), $player->getWorld());
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
            $entity = $player->getWorld()->getEntity($packet->trData->getActorRuntimeId());
            $item = TypeConverter::getInstance()->netItemStackToCore($packet->trData->getItemInHand()->getItemStack());
            $currentButton = VanillaX::getInstance()->getSessionManager()->get($player)->getInteractiveText();
            $clickPos = $packet->trData->getClickPosition();
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
                /** If a player interacts with entity with a item that has EntityInteractable traits */
                $item->onInteract(new EntityInteractResult($player, null, $entity));
            }
        }elseif($packet->trData instanceof UseItemTransactionData && $packet->trData->getActionType() === UseItemTransactionData::ACTION_CLICK_AIR){
            $entity = VanillaX::getInstance()->getSessionManager()->get($player)->getRidingEntity();
            $item = TypeConverter::getInstance()->netItemStackToCore($packet->trData->getItemInHand()->getItemStack());
            $currentButton = VanillaX::getInstance()->getSessionManager()->get($player)->getInteractiveText();

            if(is_string($currentButton) && count($packet->trData->getActions()) < 1){
                if($entity instanceof InteractButtonItemTrait){
                    /** Whenever a player interacts with interactable button for entity */
                    $entity->onButtonPressed(new InteractButtonResult($player, $item, $currentButton));
                }
                if($item instanceof InteractButtonItemTrait){
                    /** Whenever a player interacts with interactable button for item */
                    $item->onButtonPressed(new InteractButtonResult($player, $item, $currentButton));
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
        $entity = $player->getWorld()->getEntity($packet->targetActorRuntimeId);
        $session = VanillaX::getInstance()->getSessionManager()->get($player);

        if($packet->action === InteractPacket::ACTION_MOUSEOVER){
            if($packet->targetActorRuntimeId == 0 && $packet->x == 0 && $packet->y == 0 && $packet->z == 0){
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
            $packet->targetActorRuntimeId = $session->getEntityId();
        }
    }

    /**
     * @param Player $player
     * @param ActorPickRequestPacket $packet
     * This is called whenever you middle click on an entity
     */
    private function handleActorPickRequest(Player $player, ActorPickRequestPacket $packet): void{
        $entity = $player->getWorld()->getEntity($packet->actorUniqueId);

        if($entity instanceof VanillaEntity && $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
            $result = ItemFactory::getInstance()->get(ItemIds::SPAWN_EGG, EntityManager::getInstance()->getEntity($entity->getNetworkTypeId())->getId());
            $ev = new PlayerEntityPickEvent($player, $entity, $result);
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
        $manager = InventoryManager::getInstance();
        $translator = ItemTranslator::getInstance();
        $recipes = json_decode(file_get_contents(BEDROCK_DATA_PATH . "recipes.json"), true);

        $potionTypeRecipes = [];
        foreach($recipes["potion_type"] as $recipe){
            [$inputNetId, $inputNetDamage] = $translator->toNetworkId($recipe["input"]["id"], $recipe["input"]["damage"] ?? 0);
            [$ingredientNetId, $ingredientNetDamage] = $translator->toNetworkId($recipe["ingredient"]["id"], $recipe["ingredient"]["damage"] ?? 0);
            [$outputNetId, $outputNetDamage] = $translator->toNetworkId($recipe["output"]["id"], $recipe["output"]["damage"] ?? 0);
            $potion = new PotionTypeRecipe($inputNetId, $inputNetDamage, $ingredientNetId, $ingredientNetDamage, $outputNetId, $outputNetDamage);
            $packet->potionTypeRecipes[] = $potion;
            $potion = $manager->internalPotionTypeRecipe(clone $potion);
            $potionTypeRecipes[$manager->hashPotionType($potion)] = $potion;
        }

        $potionContainerRecipes = [];
        foreach($recipes["potion_container_change"] as $recipe){
            $inputNetId = $translator->toNetworkId($recipe["input_item_id"], 0)[0];
            $ingredientNetId = $translator->toNetworkId($recipe["ingredient"]["id"], 0)[0];
            $outputNetId = $translator->toNetworkId($recipe["output_item_id"], 0)[0];
            $potion = new PotionContainerChangeRecipe($inputNetId, $ingredientNetId, $outputNetId);
            $packet->potionContainerRecipes[] = $potion;
            $potion = $manager->internalPotionContainerRecipe(clone $potion);
            $potionContainerRecipes[$manager->hashPotionContainer($potion)] = $potion;
        }

        InventoryManager::getInstance()->setPotionTypeRecipes($potionTypeRecipes);
        InventoryManager::getInstance()->setPotionContainerRecipes($potionContainerRecipes);
    }

    /**
     * @param Session $session
     * @param PlayerActionPacket $packet
     * this packet is sent by player whenever they want to swim, jump, break, use elytra, etc
     */
    private function handlePlayerAction(Session $session, PlayerActionPacket $packet): void{
        if(in_array($packet->action, [PlayerAction::START_GLIDE, PlayerAction::STOP_GLIDE])){
            $session->setGliding($packet->action === PlayerAction::START_GLIDE);
        }
        if(in_array($packet->action, [PlayerAction::START_SWIMMING, PlayerAction::STOP_SWIMMING])){
            $session->setSwimming($packet->action === PlayerAction::START_SWIMMING);
        }
    }
}