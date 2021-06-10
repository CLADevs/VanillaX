<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\blocks\tiles\CommandBlockTile;
use CLADevs\VanillaX\entities\object\ArmorStandEntity;
use CLADevs\VanillaX\entities\utils\EntityInteractable;
use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use CLADevs\VanillaX\entities\utils\EntityMouseHover;
use CLADevs\VanillaX\entities\utils\EntityRidable;
use CLADevs\VanillaX\listeners\ListenerManager;
use CLADevs\VanillaX\VanillaX;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\level\Position;
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

        if(!$event->isCancelled() && $packet instanceof AvailableCommandsPacket){
            $this->handleCommandEnum($packet);
        }
    }

    public function onDataPacketReceive(DataPacketReceiveEvent $event): void{
        if(!$event->isCancelled()){
            $packet = $event->getPacket();
            $player = $event->getPlayer();
            $session = VanillaX::getInstance()->getSessionManager()->get($player);

            if(($window = $session->getCurrentWindow()) !== null) $window->handlePacket($player, $packet);
            switch($packet::NETWORK_ID){
                case ProtocolInfo::COMMAND_BLOCK_UPDATE_PACKET:
                    if($packet instanceof CommandBlockUpdatePacket) $this->handleCommandBlock($player, $packet);
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
     * Changes server sided command block tiles data
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

            if($entity instanceof EntityInteractable){
                /** If a player interacts with entity with a item */
                $entity->onInteract(new EntityInteractResult($player, $item, null, $packet->trData->getClickPos()));
                if($entity instanceof ArmorStandEntity){
                    $this->manager->armorStandItemsQueue[$player->getName()] = $packet->trData->getHotbarSlot();
                }
            }
            if($item instanceof EntityInteractable){
                /** If a player interacts with entity with a item that has EntityInteractable trait */
                $item->onInteract(new EntityInteractResult($player, null, $entity));
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

        if($packet->action === InteractPacket::ACTION_MOUSEOVER && $entity instanceof EntityMouseHover){
            $entity->onMouseHover($player);
        }elseif($packet->action === InteractPacket::ACTION_LEAVE_VEHICLE && $entity instanceof EntityRidable){
            $entity->onLeftRide($player);
        }
    }
}