<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\block\Block;
use pocketmine\block\FlowerPot;

class FlowerPotBlock extends FlowerPot implements NonAutomaticCallItemTrait{

    public function canAddPlant(Block $block): bool{
        if($block->getId() === BlockVanilla::CRIMSON_ROOTS || $block->getId() === BlockVanilla::WARPED_ROOTS){
            return true;
        }
        return parent::canAddPlant($block);
    }
}