<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\BlockIds;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\block\Block;
use pocketmine\block\FlowerPot;

class FlowerPotBlock extends FlowerPot implements NonAutomaticCallItemTrait{

    public function canAddPlant(Block $block): bool{
        if($block->getId() === BlockIds::CRIMSON_ROOTS || $block->getId() === BlockIds::WARPED_ROOTS){
            return true;
        }
        return parent::canAddPlant($block);
    }
}