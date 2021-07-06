<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\blocks\tile\FurnaceTile;
use CLADevs\VanillaX\inventories\InventoryManager;
use CLADevs\VanillaX\network\gamerules\GameRule;
use CLADevs\VanillaX\session\Session;
use pocketmine\block\BlockIds;
use pocketmine\entity\object\FallingBlock;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityBlockChangeEvent;
use pocketmine\event\inventory\FurnaceSmeltEvent;
use pocketmine\event\Listener;

class BlockListener implements Listener{

    public function onBreak(BlockBreakEvent $event): void{
        if(!$event->isCancelled()){
            $block = $event->getBlock();

            if(!GameRule::getGameRuleValue(GameRule::DO_TILE_DROPS, $block->getLevel())){
                $event->setDrops([]);
                return;
            }
            $tile = $block->getLevel()->getTile($block);

            if($tile instanceof FurnaceTile){
                $xpHolder = $tile->getXpHolder();

                if($xpHolder >= 0.1){
                    $block->getLevel()->dropExperience($block, $xpHolder * 10);
                }
            }
        }
    }

    public function onAnvilLandFall(EntityBlockChangeEvent $event): void{
        if(!$event->isCancelled()){
            $entity = $event->getEntity();

            if($entity instanceof FallingBlock && ($to = $event->getTo())->getId() === BlockIds::ANVIL){
                $pk = Session::playSound($to->asVector3(), "random.anvil_land", 1, 1, true);
                $to->getLevel()->broadcastPacketToViewers($to, $pk);
            }
        }
    }

    public function onFurnaceSmelt(FurnaceSmeltEvent $event): void{
        if(!$event->isCancelled()){
            $tile = $event->getFurnace();

            if($tile instanceof FurnaceTile){
                $xp = InventoryManager::getExpForFurnace($event->getSource());

                if($xp >= 0.1){
                    $tile->setXpHolder($tile->getXpHolder() + $xp);
                }
            }
        }
    }
}