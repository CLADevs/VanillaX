<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\BlockIds;
use CLADevs\VanillaX\items\LegacyItemIds;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;

class WarpedWartBlock extends Opaque{

    //TODO placable in Composter
    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockIds::WARPED_WART_BLOCK, 0, LegacyItemIds::WARPED_WART_BLOCK), "Warped Wart Block", new BlockBreakInfo(1, BlockToolType::HOE));
    }
}