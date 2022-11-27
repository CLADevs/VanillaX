<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\blocks\tile\FurnaceTile;
use CLADevs\VanillaX\inventories\InventoryManager;
use CLADevs\VanillaX\world\gamerule\GameRule;
use CLADevs\VanillaX\world\gamerule\GameRuleManager;
use CLADevs\VanillaX\session\Session;
use pocketmine\block\Anvil;
use pocketmine\block\BlockLegacyIds;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
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
            $drops = $event->getDrops();
            $fortuneLevel = $event->getItem()->getEnchantmentLevel(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::FORTUNE));

            if($fortuneLevel >= 1 && count($drops) === 1){
                $drop = $drops[$key = array_key_first($drops)];
                $fortune = $drop->getCount();

                switch($block->getIdInfo()->getBlockId()){
                    case BlockLegacyIds::DIAMOND_ORE:
                    case BlockLegacyIds::EMERALD_ORE:
                    case BlockLegacyIds::REDSTONE_ORE:
                    case BlockLegacyIds::NETHER_QUARTZ_ORE:
                    case BlockLegacyIds::IRON_ORE:
                    case BlockLegacyIds::GOLD_ORE:
                    case BlockLegacyIds::GLOWSTONE:
                    case BlockLegacyIds::COAL_ORE:
                        $fortune += mt_rand(0, $fortuneLevel);
                        break;
                    case BlockLegacyIds::LAPIS_ORE:
                        $fortune = mt_rand(4, 9 * ($fortuneLevel * 1));
                        break;
                }
                if($fortune !== $drop->getCount()){
                    $drop->setCount($fortune);
                    $drops[$key] = $drop;
                    $event->setDrops($drops);
                }
            }
        }
    }

    public function onAnvilLandFall(EntityBlockChangeEvent $event): void{
        if(!$event->isCancelled()){
            $entity = $event->getEntity();
            $to = $event->getTo();

            if($entity instanceof FallingBlock && $to instanceof Anvil){
                $pk = Session::playSound($to->getPosition(), "random.anvil_land", 1, 1, true);
                $to->getPosition()->getWorld()->broadcastPacketToViewers($to->getPosition(), $pk);
            }
        }
    }

    public function onFurnaceSmelt(FurnaceSmeltEvent $event): void{
        if(!$event->isCancelled()){
            $tile = $event->getFurnace();

            if($tile instanceof FurnaceTile){
                $xp = InventoryManager::getInstance()->getExpForFurnace($event->getSource());

                if($xp >= 0.1){
                    $tile->setXpHolder($tile->getXpHolder() + $xp);
                }
            }
        }
    }
}
