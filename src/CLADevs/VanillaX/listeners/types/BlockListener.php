<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\blocks\tile\FurnaceTile;
use CLADevs\VanillaX\inventories\InventoryManager;
use CLADevs\VanillaX\world\gamerule\GameRule;
use CLADevs\VanillaX\world\gamerule\GameRuleManager;
use CLADevs\VanillaX\session\Session;
use pocketmine\block\BlockLegacyIds;
use pocketmine\entity\object\FallingBlock;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityBlockChangeEvent;
use pocketmine\event\inventory\FurnaceSmeltEvent;
use pocketmine\event\Listener;

class BlockListener implements Listener{

    public function onBreak(BlockBreakEvent $event): void{
        if(!$event->isCancelled()){
            $block = $event->getBlock();
            if(!GameRuleManager::getInstance()->getValue(GameRule::DO_TILE_DROPS, $block->getPosition()->getWorld())){
                $event->setDrops([]);
                return;
            }
            $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
            if($tile instanceof FurnaceTile){
                $tile->dropXpHolder($block->getPosition());
            }
        }
    }

    public function onAnvilLandFall(EntityBlockChangeEvent $event): void{
        if(!$event->isCancelled()){
            $entity = $event->getEntity();
            if($entity instanceof FallingBlock && ($to = $event->getTo())->getId() === BlockLegacyIds::ANVIL){
                $pk = Session::playSound($to->getPosition(), "random.anvil_land", 1, 1, true);
                $to->getPosition()->getWorld()->broadcastPacketToViewers($to->getPosition(), $pk);
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
