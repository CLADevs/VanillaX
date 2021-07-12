<?php

namespace CLADevs\VanillaX\blocks\block;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Transparent;

class BubbleColumnBlock extends Transparent{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::BUBBLE_COLUMN, 0), "Bubble Column", new BlockBreakInfo(0, BlockToolType::NONE, 0, 100));
    }
}