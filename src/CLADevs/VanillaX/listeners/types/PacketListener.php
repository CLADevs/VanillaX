<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\blocks\tile\CommandBlockTile;
use CLADevs\VanillaX\entities\utils\EntityButtonResult;
use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use CLADevs\VanillaX\entities\utils\interfaces\EntityInteractable;
use CLADevs\VanillaX\entities\utils\interfaces\EntityInteractButton;
use CLADevs\VanillaX\entities\utils\interfaces\EntityRidable;
use CLADevs\VanillaX\listeners\ListenerManager;
use CLADevs\VanillaX\VanillaX;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBlockPickEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\ActorPickRequestPacket;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\SetDefaultGameTypePacket;
use pocketmine\network\mcpe\protocol\SetDifficultyPacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData;
use pocketmine\Player;
use pocketmine\Server;

class PacketListener implements Listener{

    private ListenerManager $manager;

    public function __construct(ListenerManager $manager){
        $this->manager = $manager;
    }

    public function onInventoryTransaction(InventoryTransactionEvent $event): void{
        VanillaX::getInstance()->getEnchantmentManager()->handleInventoryTransaction($event);
    }

    public function onDataPacketSend(DataPacketSendEvent $event): void{
        $packet = $event->getPacket();

        if(!$event->isCancelled()){
            $player = $event->getPlayer();

            switch($packet::NETWORK_ID){
                case ProtocolInfo::AVAILABLE_COMMANDS_PACKET:
                    if($packet instanceof AvailableCommandsPacket) $this->handleCommandEnum($packet);
                    break;
                case ProtocolInfo::ADD_PLAYER_PACKET:
                    if($packet instanceof AddPlayerPacket){
                        $p = Server::getInstance()->getPlayer($packet->username);

                        if($p !== null){
                            VanillaX::getInstance()->getSessionManager()->get($player)->getOffHandInventory()->sendContents();
                            VanillaX::getInstance()->getSessionManager()->get($p)->getOffHandInventory()->sendContents();
                        }
                    }
                    break;
            }
        }
    }

    public function onDataPacketReceive(DataPacketReceiveEvent $event): void{
        if(!$event->isCancelled()){
            $packet = $event->getPacket();
            $player = $event->getPlayer();
            $sessionManager = VanillaX::getInstance()->getSessionManager();
            $session = $sessionManager->has($player) ? $sessionManager->get($player) : null;

            if($session !== null && ($window = $session->getCurrentWindow()) !== null) $window->handlePacket($player, $packet);
            switch($packet::NETWORK_ID){
                case ProtocolInfo::COMMAND_BLOCK_UPDATE_PACKET:
                    if($packet instanceof CommandBlockUpdatePacket) $this->handleCommandBlock($player, $packet);
                    break;
                case ProtocolInfo::PLAYER_ACTION_PACKET:
                    if($packet instanceof PlayerActionPacket && in_array($packet->action, [PlayerActionPacket::ACTION_START_GLIDE, PlayerActionPacket::ACTION_STOP_GLIDE])){
                        $session->getGliding()->setGliding($packet->action === PlayerActionPacket::ACTION_START_GLIDE);
                    }
                    break;
                case ProtocolInfo::INVENTORY_TRANSACTION_PACKET:
                    if($packet instanceof InventoryTransactionPacket) $this->handleInventoryTransaction($player, $packet);
                    break;
                case ProtocolInfo::SET_PLAYER_GAME_TYPE_PACKET:
                    /** Server Form Personal Game Type Setting */
                    if($player->isOp() && $packet instanceof SetPlayerGameTypePacket){
                        $player->setGamemode($packet->gamemode);
                    }
                    break;
                case ProtocolInfo::SET_DEFAULT_GAME_TYPE_PACKET:
                    /** Server Form Default Game Type Setting */
                    if($player->isOp() && $packet instanceof SetDefaultGameTypePacket){
                        Server::getInstance()->setConfigInt("gamemode", $packet->gamemode);
                    }
                    break;
                case ProtocolInfo::SET_DIFFICULTY_PACKET:
                    /** Server Form Difficulty Setting */
                    if($player->isOp() && $packet instanceof SetDifficultyPacket){
                        $player->getLevel()->setDifficulty($packet->difficulty);
                    }
                    break;
                case ProtocolInfo::CONTAINER_CLOSE_PACKET:
                    /** Fixes Trading GUI issue */
                    if($packet instanceof ContainerClosePacket && $packet->windowId === 255){
                        $player->dataPacket($packet);
                    }
                    break;
                case ProtocolInfo::INTERACT_PACKET:
                    if($packet instanceof InteractPacket) $this->handleInteract($player, $packet);
                    break;
                case ProtocolInfo::ACTOR_PICK_REQUEST_PACKET:
                    if($packet instanceof ActorPickRequestPacket) $this->handleActorPickRequest($player, $packet);
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
        $position = new Position($packet->x, $packet->y, $packet->z, $player->getLevel());
        $tile = $position->getLevel()->getTile($position);

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
            $entity = $player->getLevel()->getEntity($packet->trData->getEntityRuntimeId());
            $item = $packet->trData->getItemInHand()->getItemStack();
            $currentButton = $player->getDataPropertyManager()->getString(Entity::DATA_INTERACTIVE_TAG);
            $clickPos = $packet->trData->getClickPos();
            $button = null;

            if(is_string($currentButton) && $entity instanceof EntityInteractButton && count($packet->trData->getActions()) < 1){
                /** Whenever a player interacts with interactable button */
                $entity->onButtonPressed($button = new EntityButtonResult($player, $item, $currentButton, $clickPos));
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
            $item = $packet->trData->getItemInHand()->getItemStack();
            $currentButton = $player->getDataPropertyManager()->getString(Entity::DATA_INTERACTIVE_TAG);

            if(is_string($currentButton) && $entity instanceof EntityInteractButton && count($packet->trData->getActions()) < 1){
                /** Whenever a player interacts with interactable button */
                $entity->onButtonPressed(new EntityButtonResult($player, $item, $currentButton));
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
        $entity = $player->getLevel()->getEntity($packet->target);
        $session = VanillaX::getInstance()->getSessionManager()->get($player);

        if($packet->action === InteractPacket::ACTION_MOUSEOVER){
            if($packet->target == 0 && $packet->x == 0 && $packet->y == 0 && $packet->z == 0){
                $entity = $session->getRidingEntity();

                if($entity === null){
                    $player->getDataPropertyManager()->setString(Entity::DATA_INTERACTIVE_TAG, "");
                }
            }elseif($entity instanceof EntityInteractButton){
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
        $entity = $player->getLevel()->getEntity($packet->entityUniqueId);

        if($entity instanceof Entity && !$entity instanceof Human){
            $result = ItemFactory::get(ItemIds::SPAWN_EGG, $entity::NETWORK_ID);

            $ev = new PlayerBlockPickEvent($player, BlockFactory::get(BlockIds::AIR), $result);
            $ev->call(); //This will call vanillax PlayerBlockPickEvent event and calculates slot

            if(!$ev->isCancelled()){
                $player->getInventory()->setItemInHand($ev->getResultItem());
            }
        }
    }
}