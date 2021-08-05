<?php

namespace CLADevs\VanillaX\blocks\block\fence;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Fence;

class WarpedFenceBlock extends Fence{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::WARPED_FENCE, 0, ItemIdentifiers::WARPED_FENCE), "Warped Fence", new BlockBreakInfo(2, BlockToolType::AXE, 0, 3));
    }
}