<?php

namespace CLADevs\VanillaX\blocks\block\redstone\rails;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockLegacyMetadata;
use pocketmine\block\PoweredRail as PMPoweredRail;

class PoweredRail extends PMPoweredRail{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::GOLDEN_RAIL, BlockLegacyMetadata::RAIL_STRAIGHT_NORTH_SOUTH), "Detector Rail", new BlockBreakInfo(0.7));
    }
}