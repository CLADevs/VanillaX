<?php

namespace CLADevs\VanillaX;

use CLADevs\VanillaX\blocks\tiles\CommandBlockTile;
use CLADevs\VanillaX\inventories\EnchantInventory;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;

class VanillaListener implements Listener{

    public function handlePacketReceive(DataPacketReceiveEvent $event): void{
        $packet = $event->getPacket();
        $player = $event->getPlayer();

        if($packet instanceof CommandBlockUpdatePacket){
            $position = new Position($packet->x, $packet->y, $packet->z, $player->getLevel());
            $tile = $position->getLevel()->getTile($position);

            if($tile instanceof CommandBlockTile){
                $tile->handleCommandBlockUpdateReceive($packet);
            }
        }else{
            /** Enchantment Table */
            if($packet instanceof InventoryTransactionPacket || $packet instanceof PlayerActionPacket){
                $window = $player->getWindow(WindowTypes::ENCHANTMENT);

                if($window instanceof EnchantInventory){
                    $window->handlePacket($player, $packet);
                    return;
                }
            }
//            /** Anvil */
//            if($packet instanceof InventoryTransactionPacket || $packet instanceof FilterTextPacket || $packet instanceof AnvilDamagePacket){
//                $window = $player->getWindow(WindowTypes::ANVIL);
//
//                if($window instanceof AnvilInventory){
//                    $window->handlePacket($player, $packet);
//                }
//            }
        }
    }
}