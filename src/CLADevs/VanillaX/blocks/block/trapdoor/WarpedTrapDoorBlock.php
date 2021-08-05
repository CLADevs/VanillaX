<?php

namespace CLADevs\VanillaX\blocks\block\trapdoor;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\WoodenTrapdoor;

class WarpedTrapDoorBlock extends WoodenTrapdoor{

    //TODO functionality
    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::WARPED_TRAPDOOR, 0, ItemIdentifiers::WARPED_TRAPDOOR), "Warped Trapdoor", new BlockBreakInfo(3, BlockToolType::AXE));
    }
}