<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\BlockIds;
use CLADevs\VanillaX\items\LegacyItemIds;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Transparent;
use pocketmine\math\Vector3;

class HangingRootsBlock extends Transparent{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockIds::HANGING_ROOTS, 0, LegacyItemIds::HANGING_ROOTS), "Hanging Roots", new BlockBreakInfo(0.1, BlockToolType::SHEARS, 0, 0.1));
    }

    public function canBePlacedAt(Block $blockReplace, Vector3 $clickVector, int $face, bool $isClickedBlock): bool{
        if($this->position->getWorld()->getBlock(clone $this->position->add(0, 1, 0)) instanceof Air){
           return false;
        }
        return parent::canBePlacedAt($blockReplace, $clickVector, $face, $isClickedBlock);
    }
}