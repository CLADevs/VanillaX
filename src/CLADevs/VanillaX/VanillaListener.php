<?php

namespace CLADevs\VanillaX;

use CLADevs\VanillaX\blocks\tiles\CommandBlockTile;

use CLADevs\VanillaX\entities\traits\EntityInteractable;
use CLADevs\VanillaX\inventories\EnchantInventory;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\SetDefaultGameTypePacket;
use pocketmine\network\mcpe\protocol\SetDifficultyPacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;
use pocketmine\Server;

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
        }elseif($packet instanceof InventoryTransactionPacket && $packet->transactionType === InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY){
            if($packet->trData->actionType === InventoryTransactionPacket::USE_ITEM_ON_ENTITY_ACTION_INTERACT){
                $entity = $player->getLevel()->getEntity($packet->trData->entityRuntimeId);
                $item = $packet->trData->itemInHand;

                if($entity instanceof EntityInteractable){
                    $entity->onInteract($player, $item);
                }
                if($item instanceof EntityInteractable){
                    $item->onInteractWithEntity($player, $entity);
                }
            }
        }
        if($player->isOp()){
            if($packet instanceof SetPlayerGameTypePacket){
                $player->setGamemode($packet->gamemode);
            }elseif($packet instanceof SetDefaultGameTypePacket){
                Server::getInstance()->setConfigInt("gamemode", $packet->gamemode);
            }elseif($packet instanceof SetDifficultyPacket){
                $player->getLevel()->setDifficulty($packet->difficulty);
            }
        }
    }

    public function onDamage(EntityDamageEvent $event): void{
        if(!$event->isCancelled() && $event->getCause() === EntityDamageEvent::CAUSE_FALL){
            $entity = $event->getEntity();

            if($entity instanceof Player){
                //$en = new StriderEntity($entity->getLevel(), StriderEntity::createBaseNBT($entity));
                //if($en->getAgeable() !== null){
                    //$en->getAgeable()->setBaby(true);
                //}
                //$en->spawnToAll();
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

    public function onTransaction(InventoryTransactionEvent $event): void{
        VanillaX::getInstance()->getEnchantmentManager()->handleReceivedEvent($event);
    }

    public function onQuit(PlayerQuitEvent $event): void{
        $player = $event->getPlayer();
        $manager = VanillaX::getInstance()->getSessionManager();
        $session = $manager->get($player);

        foreach($session->getThrownTridents() as $entity){
            if($entity->isAlive() && !$entity->isFlaggedForDespawn()){
                $entity->onCollideWithPlayer($player);
            }
        }
        $manager->remove($player);
    }

//    public function onEntitySpawn(EntitySpawnEvent $event): void{
//        $entity = $event->getEntity();
//
//        if($entity instanceof ItemEntity){
//            $tiles = $entity->getLevel()->getChunkAtPosition($entity)->getTiles();
//
//            foreach($tiles as $tile){
//                if($tile instanceof HopperTile){
//                   $tile->onDrop();
//                }
//            }
//        }
//    }
}