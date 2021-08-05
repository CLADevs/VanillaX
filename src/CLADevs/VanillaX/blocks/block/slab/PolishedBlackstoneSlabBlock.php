<?php

namespace CLADevs\VanillaX\blocks\block\slab;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifierFlattened;
use pocketmine\block\BlockToolType;
use pocketmine\block\Slab;

class PolishedBlackstoneSlabBlock extends Slab{

    //TODO fix upper block runtime id
    public function __construct(){
        parent::__construct(new BlockIdentifierFlattened(BlockVanilla::POLISHED_BLACKSTONE_SLAB, [BlockVanilla::POLISHED_BLACKSTONE_DOUBLE_SLAB], 0), "Polished Blackstone Slab", new BlockBreakInfo(2.0, BlockToolType::AXE, 0, 6));
    }
}