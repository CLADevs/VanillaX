<?php

namespace CLADevs\VanillaX\blocks\block\stem;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;

class WarpedStemBlock extends Opaque{

    //TODO functionality
    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::WARPED_STEM, 0, ItemIdentifiers::WARPED_STEM), "Warped Stem", new BlockBreakInfo(2, BlockToolType::AXE));
    }
}