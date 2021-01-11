<?php

namespace CLADevs\VanillaX;

use CLADevs\VanillaX\blocks\tiles\CommandBlockTile;
use CLADevs\VanillaX\entities\passive\StriderEntity;
use CLADevs\VanillaX\inventories\EnchantInventory;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

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
        if($packet instanceof PlayerActionPacket && in_array($packet->action, [PlayerActionPacket::ACTION_START_GLIDE, PlayerActionPacket::ACTION_STOP_GLIDE])){
            $session = VanillaX::getInstance()->getSessionManager()->get($player);
            $session->setGliding($packet->action === PlayerActionPacket::ACTION_START_GLIDE);
        }
    }

    public function onDamage(EntityDamageEvent $event): void{
        if(!$event->isCancelled() && $event->getCause() === EntityDamageEvent::CAUSE_FALL){
            $entity = $event->getEntity();

            if($entity instanceof Player){
                $en = new StriderEntity($entity->getLevel(), StriderEntity::createBaseNBT($entity));
                if($en->getAgeable() !== null){
                    $en->getAgeable()->setBaby(true);
                }
                $en->spawnToAll();
                $session = VanillaX::getInstance()->getSessionManager()->get($entity);

                if($session->isGliding()){
                    $event->setCancelled();
                }else{
                    if(($end = $session->getEndGlideTime()) !== null && ($start = $session->getStartGlideTime()) !== null){
                        if(($end - $start) < 3){
                            $event->setCancelled();
                        }
                    }
                }
            }
        }
    }
}