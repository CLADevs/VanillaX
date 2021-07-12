<?php

namespace CLADevs\VanillaX\blocks\block\redstone\pressurePlates;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\StonePressurePlate as PMStonePressurePlate;
use pocketmine\item\ToolTier;

class StonePressurePlate extends PMStonePressurePlate{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::STONE_PRESSURE_PLATE, 0), "Stone Pressure Plate", new BlockBreakInfo(0.5, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel()));
    }
}