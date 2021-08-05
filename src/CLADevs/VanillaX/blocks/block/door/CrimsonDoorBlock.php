<?php

namespace CLADevs\VanillaX\blocks\block\door;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Door;

class CrimsonDoorBlock extends Door{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::CRIMSON_DOOR, 0, ItemIdentifiers::CRIMSON_DOOR), "Crimson Door", new BlockBreakInfo(3, BlockToolType::AXE));
    }
}