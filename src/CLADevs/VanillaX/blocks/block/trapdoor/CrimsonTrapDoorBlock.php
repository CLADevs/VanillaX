<?php

namespace CLADevs\VanillaX\blocks\block\trapdoor;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\WoodenTrapdoor;

class CrimsonTrapDoorBlock extends WoodenTrapdoor{

    //TODO functionality
    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::CRIMSON_TRAPDOOR, 0, ItemIdentifiers::CRIMSON_TRAPDOOR), "Crimson Trapdoor", new BlockBreakInfo(3, BlockToolType::AXE));
    }
}