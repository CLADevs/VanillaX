<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;

class WarpedWartBlock extends Opaque{

    //TODO functionality
    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::WARPED_WART_BLOCK, 0, ItemIdentifiers::WARPED_WART_BLOCK), "Warped Wart Block", new BlockBreakInfo(1, BlockToolType::HOE));
    }
}