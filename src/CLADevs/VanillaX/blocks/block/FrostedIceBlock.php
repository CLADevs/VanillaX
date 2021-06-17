<?php

namespace CLADevs\VanillaX\blocks\block;

use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\block\Ice;

class FrostedIceBlock extends Ice{

    protected $id = self::FROSTED_ICE;

    const TYPE_NORMAL = 0;
    const TYPE_SLIGHTLY_CRACKED = 1;
    const TYPE_CRACKED = 2;
    const TYPE_VERY_CRACKED = 3;

    public function getName(): string{
        return "Frosted Ice";
    }

    public function onNearbyBlockChange(): void{
        $this->level->scheduleDelayedBlockUpdate($this, 15);
    }

    public function onScheduledUpdate(): void{
        $this->onRandomTick();
    }

    public function onRandomTick(): void{
        if($this->getDamage() === 4){
            $this->getLevel()->setBlock($this, BlockFactory::get(BlockIds::WATER), true);
        }else{
            $block = clone $this;
            $block->setDamage($this->meta++);
            $this->getLevel()->setBlock($this, $block, true);
            $this->level->scheduleDelayedBlockUpdate($this, 15);
        }
    }
}