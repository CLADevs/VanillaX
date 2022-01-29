<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\BlockIds;
use CLADevs\VanillaX\blocks\utils\BlockFacingOppositeTrait;
use CLADevs\VanillaX\items\LegacyItemIds;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Transparent;

class ChainBlock extends Transparent{
    use BlockFacingOppositeTrait;

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockIds::CHAIN, 0, LegacyItemIds::CHAIN), "Chain", new BlockBreakInfo(5, BlockToolType::PICKAXE, 0, 6));
    }
}