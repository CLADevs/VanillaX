<?php

namespace CLADevs\VanillaX\blocks\block\nylium;

use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Facing;

class Nylium extends Block implements NonAutomaticCallItemTrait{

    public function onRandomTick(): void{
        if($this->getSide(Facing::UP)->isSolid()){
           $this->position->getWorld()->setBlock($this->position, VanillaBlocks::NETHERRACK());
        }
    }

    public function ticksRandomly(): bool{
        return true;
    }
}