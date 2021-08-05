<?php

namespace CLADevs\VanillaX\blocks\block\slab;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifierFlattened;
use pocketmine\block\BlockToolType;
use pocketmine\block\WoodenSlab;

class WarpedSlabBlock extends WoodenSlab{

    //TODO fix upper block runtime id
    public function __construct(){
        parent::__construct(new BlockIdentifierFlattened(BlockVanilla::WARPED_SLAB, [BlockVanilla::WARPED_DOUBLE_SLAB], 0), "Warped Slab", new BlockBreakInfo(2.0, BlockToolType::AXE, 0, 6));
    }
}