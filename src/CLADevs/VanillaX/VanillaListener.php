<?php

namespace CLADevs\VanillaX;

use CLADevs\VanillaX\blocks\tiles\CommandBlockTile;
use CLADevs\VanillaX\entities\utils\EntityInteractable;
use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use CLADevs\VanillaX\items\ItemManager;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\SetDefaultGameTypePacket;
use pocketmine\network\mcpe\protocol\SetDifficultyPacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\Player;
use pocketmine\Server;

class VanillaListener implements Listener{


    public function handlePacketSend(DataPacketSendEvent $event): void{
        //TODO command args
    }

    public function handlePacketReceive(DataPacketReceiveEvent $event): void{
        $packet = $event->getPacket();
        $player = $event->getPlayer();
        $session = VanillaX::getInstance()->getSessionManager()->get($player);

        if($packet instanceof CommandBlockUpdatePacket){
            $position = new Position($packet->x, $packet->y, $packet->z, $player->getLevel());
            $tile = $position->getLevel()->getTile($position);

            if($tile instanceof CommandBlockTile){
                $tile->handleCommandBlockUpdateReceive($packet);
            }
        }else{
            $window = $session->getCurrentWindow();

            if($window !== null){
                $window->handlePacket($player, $packet);
            }
        }
        if($packet instanceof PlayerActionPacket && in_array($packet->action, [PlayerActionPacket::ACTION_START_GLIDE, PlayerActionPacket::ACTION_STOP_GLIDE])){
            $session->setGliding($packet->action === PlayerActionPacket::ACTION_START_GLIDE);
        }elseif($packet instanceof InventoryTransactionPacket && $packet->trData instanceof UseItemOnEntityTransactionData){
            if($packet->trData->getActionType() === UseItemOnEntityTransactionData::ACTION_INTERACT){
                $entity = $player->getLevel()->getEntity($packet->trData->getEntityRuntimeId());
                $item = $packet->trData->getItemInHand()->getItemStack();

                if($entity instanceof EntityInteractable){
                    /** If a player interacts with entity with a item */
                    $entity->onInteract(new EntityInteractResult($player, $item));
                }
                if($item instanceof EntityInteractable){
                    /** If a player interacts with entity with a item that has EntityInteractable trait */
                    $item->onInteract(new EntityInteractResult($player, null, $entity));
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

        /** Fixes trade gui not opening for second time bug */
        if($packet instanceof ContainerClosePacket && $packet->windowId === 255){
            $player->dataPacket($packet);
        }
    }

    public function onDamage(EntityDamageEvent $event): void{
        VanillaX::getInstance()->getEnchantmentManager()->handleDamage($event);

        if(!$event->isCancelled() && $event->getCause() === EntityDamageEvent::CAUSE_FALL){
            $entity = $event->getEntity();

            if($entity instanceof Player){
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
        VanillaX::getInstance()->getEnchantmentManager()->handleInventoryTransaction($event);
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

    public function onInteract(PlayerInteractEvent $event): void{
        $player = $event->getPlayer();
        $item = $event->getItem();

        if(($slot = ItemManager::getArmorSlot($item, true)) !== null){
            if($player->getArmorInventory()->getItem($slot)->isNull()){
                $player->getArmorInventory()->setItem($slot, $item);
                $item->pop();
                $player->getInventory()->setItemInHand($item);
            }
        }
    }
}