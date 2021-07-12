<?php

namespace CLADevs\VanillaX\blocks\block\redstone\rails;

use pocketmine\block\ActivatorRail as PMActivatorRail;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;

class ActivatorRail extends PMActivatorRail{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::ACTIVATOR_RAIL, 0), "Powered Rail", new BlockBreakInfo(0.7));
    }
}