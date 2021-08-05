<?php

namespace CLADevs\VanillaX\blocks\block\slab;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifierFlattened;
use pocketmine\block\BlockToolType;
use pocketmine\block\Slab;

class BlackstoneSlabBlock extends Slab{

    //TODO fix upper block runtime id
    public function __construct(){
        parent::__construct(new BlockIdentifierFlattened(BlockVanilla::BLACKSTONE_SLAB, [BlockVanilla::BLACKSTONE_DOUBLE_SLAB], 0), "Blackstone Slab", new BlockBreakInfo(2.0, BlockToolType::AXE, 0, 6));
    }
}