<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\inventories\FakeBlockInventory;
use CLADevs\VanillaX\network\handler\ItemStackTranslator;
use CLADevs\VanillaX\session\SessionManager;
use CLADevs\VanillaX\VanillaX;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\InventoryContentPacket;
use pocketmine\network\mcpe\protocol\InventorySlotPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\StartGamePacket;

class PacketListener implements Listener{

    public function onDataPacketSend(DataPacketSendEvent $event): void{
        if(!$event->isCancelled()){
            foreach($event->getTargets() as $target){
                foreach($event->getPackets() as $packet){
                    switch($packet::NETWORK_ID){
                        case ProtocolInfo::AVAILABLE_COMMANDS_PACKET:
                            if($packet instanceof AvailableCommandsPacket) $this->handleAvailableCommands($packet);
                            break;
                        case ProtocolInfo::START_GAME_PACKET:
                            if($packet instanceof StartGamePacket) $this->handleStartGame($packet);
                            break;
                        case ProtocolInfo::INVENTORY_CONTENT_PACKET:
                            if($packet instanceof InventoryContentPacket) $this->handleInventoryContent($target, $packet);
                            break;
                        case ProtocolInfo::INVENTORY_SLOT_PACKET:
                            if($packet instanceof InventorySlotPacket) $this->handleInventorySlot($target, $packet);
                            break;
                        case ProtocolInfo::CONTAINER_OPEN_PACKET:
                            if($packet instanceof ContainerOpenPacket) $this->handleContainerOpen($target, $packet);
                            break;
                        case ProtocolInfo::CONTAINER_CLOSE_PACKET:
                            if($packet instanceof ContainerClosePacket) $this->handleContainerClose($target, $packet);
                            break;
                    }
                }
            }
        }
    }

    public function onDataPacketReceive(DataPacketReceiveEvent $event): void{
        if(!$event->isCancelled() && ($player = $event->getOrigin()->getPlayer()) !== null){
            $packet = $event->getPacket();
            $window = $player->getCurrentWindow();

            if($window instanceof FakeBlockInventory && !$window->handlePacket($player, $packet)){
                $event->cancel();
            }
        }
    }

    /**
     * @param AvailableCommandsPacket $packet
     * Modifies enums for commands, arguments you see once you type /weather
     * are modified through this packet. '<clear: rain: thunder> [duration: int]'
     */
    private function handleAvailableCommands(AvailableCommandsPacket $packet): void{
        foreach(VanillaX::getInstance()->getCommandManager()->getCommands() as $key => $command){
            if(($arg = $command->getCommandArg()) !== null && ($command = $packet->commandData[strtolower($key)] ?? null) !== null){
                $command->flags = $arg->getFlags();
                $command->permission = $arg->getPermission();
                $command->overloads = $arg->getOverload();
            }
        }
    }

    /**
     * @param StartGamePacket $packet
     * This packet is sent by server to tell client where they are.
     * There are more to this but its alot, so we are using it for
     * to enable new inventory system made in 1.16.
     */
    private function handleStartGame(StartGamePacket $packet): void{
        $packet->enableNewInventorySystem = true;
    }

    /**
     * @param NetworkSession $networkSession
     * @param InventoryContentPacket $packet
     * Whenever certain inventory contents are changed
     */
    private function handleInventoryContent(NetworkSession $networkSession, InventoryContentPacket $packet): void{
        foreach ($packet->items as $index => $item){
            $located = $networkSession->getInvManager()->locateWindowAndSlot($packet->windowId, $index);

            if($located === null){
                continue;
            }
            [$inventory] = $located;

            if($inventory === null){
                continue;
            }
            $session = SessionManager::getInstance()->get($networkSession->getPlayer());
            $slot = ItemStackTranslator::clientSlot($index, $inventory);
            $currentItem = TypeConverter::getInstance()->netItemStackToCore($item->getItemStack());
            $session->trackItemStack($inventory, $slot, $currentItem, null);
        }
    }

    /**
     * @param NetworkSession $networkSession
     * @param InventorySlotPacket $packet
     * Whenever certain inventory slot is item is changed
     */
    private function handleInventorySlot(NetworkSession $networkSession, InventorySlotPacket $packet): void{
        $located = $networkSession->getInvManager()->locateWindowAndSlot($packet->windowId, $packet->inventorySlot);

        if($located === null){
            return;
        }
        [$inventory] = $located;

        if($inventory === null){
            return;
        }

        $session = SessionManager::getInstance()->get($networkSession->getPlayer());
        $slot = ItemStackTranslator::clientSlot($packet->inventorySlot, $inventory);
        $currentItem = TypeConverter::getInstance()->netItemStackToCore($packet->item->getItemStack());
        $session->trackItemStack($inventory, $slot, $currentItem, null);
    }

    /**
     * @param NetworkSession $networkSession
     * @param ContainerOpenPacket $packet
     * Whenever certain inventory is opened
     */
    private function handleContainerOpen(NetworkSession $networkSession, ContainerOpenPacket $packet): void{
        SessionManager::getInstance()->get($networkSession->getPlayer())->onContainerOpen($packet->windowId);
    }

    /**
     * @param NetworkSession $networkSession
     * @param ContainerClosePacket $packet
     * Whenever certain inventory is closed
     */
    private function handleContainerClose(NetworkSession $networkSession, ContainerClosePacket $packet): void{
        SessionManager::getInstance()->get($networkSession->getPlayer())->onContainerClose($packet->windowId);
    }
}