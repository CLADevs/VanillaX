<?php

namespace CLADevs\VanillaX\blocks\block\redstone;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Redstone;
use pocketmine\item\ToolTier;

class RedstoneBlock extends Redstone{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::REDSTONE_BLOCK, 0), "Redstone Block", new BlockBreakInfo(5.0, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel(), 30.0));
    }
}