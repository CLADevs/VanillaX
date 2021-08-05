<?php

namespace CLADevs\VanillaX\blocks\block\redstone\pressurePlates;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\SimplePressurePlate;

class WarpedPressurePlate extends SimplePressurePlate{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::WARPED_PRESSURE_PLATE, 0, ItemIdentifiers::WARPED_PRESSURE_PLATE), "Warped Pressure Plate", new BlockBreakInfo(0.5, BlockToolType::PICKAXE));
    }
}