<?php

namespace CLADevs\VanillaX\blocks\block\slab;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifierFlattened;
use pocketmine\block\BlockToolType;
use pocketmine\block\Slab;

class PolishedBlackstoneBrickSlabBlock extends Slab{

    //TODO fix upper block runtime id
    public function __construct(){
        parent::__construct(new BlockIdentifierFlattened(BlockVanilla::POLISHED_BLACKSTONE_BRICK_SLAB, [BlockVanilla::POLISHED_BLACKSTONE_BRICK_DOUBLE_SLAB], 0), "Polished Blackstone Brick Slab", new BlockBreakInfo(2.0, BlockToolType::AXE, 0, 6));
    }
}