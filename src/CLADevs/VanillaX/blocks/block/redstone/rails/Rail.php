<?php

namespace CLADevs\VanillaX\blocks\block\redstone\rails;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Rail as PMRail;

class Rail extends PMRail{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::RAIL, 0), "Rail", new BlockBreakInfo(0.7));
    }
}