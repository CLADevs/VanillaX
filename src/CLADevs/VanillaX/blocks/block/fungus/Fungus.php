<?php

namespace CLADevs\VanillaX\blocks\block\fungus;

use CLADevs\VanillaX\blocks\BlockIds;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Transparent;
use pocketmine\math\Vector3;

class Fungus extends Transparent implements NonAutomaticCallItemTrait{

    public function __construct(BlockIdentifier $blockInfo, string $name = ""){
        parent::__construct($blockInfo, $name, BlockBreakInfo::instant());
    }

    public function canBePlacedAt(Block $blockReplace, Vector3 $clickVector, int $face, bool $isClickedBlock): bool{
        $block = $this->position->getWorld()->getBlock(clone $this->position->subtract(0, 1, 0));

        if(!in_array($block->getId(), [BlockLegacyIds::GRASS, BlockLegacyIds::DIRT, BlockLegacyIds::PODZOL, BlockLegacyIds::FARMLAND, BlockIds::DIRT_WITH_ROOTS, BlockIds::CRIMSON_NYLIUM, BlockIds::WARPED_NYLIUM])){
            return false;
        }
        return parent::canBePlacedAt($blockReplace, $clickVector, $face, $isClickedBlock);
    }
}
