<?php

namespace CLADevs\VanillaX\blocks\block\nylium;

use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Facing;

class Nylium extends Block implements NonAutomaticCallItemTrait{

    public function onRandomTick(): void{
        if($this->getSide(Facing::UP)->isSolid()){
           $this->pos->getWorld()->setBlock($this->pos, VanillaBlocks::NETHERRACK());
        }
    }

    public function ticksRandomly(): bool{
        return true;
    }
}