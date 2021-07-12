<?php

namespace CLADevs\VanillaX\blocks\block\redstone;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifierFlattened;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\RedstoneTorch as PMRedstoneTorch;

class RedstoneTorch extends PMRedstoneTorch{

    public function __construct(){
        parent::__construct(new BlockIdentifierFlattened(BlockLegacyIds::REDSTONE_TORCH, [BlockLegacyIds::UNLIT_REDSTONE_TORCH], 0), "Redstone Torch", BlockBreakInfo::instant());
    }
}