<?php

namespace CLADevs\VanillaX\blocks\block\redstone\rails;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\DetectorRail as PMDetectorRail;

class DetectorRail extends PMDetectorRail{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::DETECTOR_RAIL, 0), "Detector Rail", new BlockBreakInfo(0.7));
    }
}