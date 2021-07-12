<?php

namespace CLADevs\VanillaX\blocks\block\redstone\buttons;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\StoneButton as PMStoneButton;

class StoneButton extends PMStoneButton{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::STONE_BUTTON, 0), "Stone Button", new BlockBreakInfo(0.5, BlockToolType::PICKAXE));
    }
}