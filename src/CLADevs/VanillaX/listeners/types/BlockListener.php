<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\network\gamerules\GameRule;
use CLADevs\VanillaX\session\Session;
use pocketmine\block\BlockIds;
use pocketmine\entity\object\FallingBlock;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityBlockChangeEvent;
use pocketmine\event\Listener;

class BlockListener implements Listener{

    public function onBreak(BlockBreakEvent $event): void{
        if(!$event->isCancelled() && !GameRule::getGameRuleValue(GameRule::DO_TILE_DROPS, $event->getBlock()->getLevel())){
            $event->setDrops([]);
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
}