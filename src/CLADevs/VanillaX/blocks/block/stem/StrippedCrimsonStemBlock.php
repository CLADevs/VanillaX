<?php

namespace CLADevs\VanillaX\blocks\block\stem;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;

class StrippedCrimsonStemBlock extends Opaque{

    //TODO functionality
    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::STRIPPED_CRIMSON_STEM, 0, ItemIdentifiers::STRIPPED_CRIMSON_STEM), "Stripped Crimson Stem", new BlockBreakInfo(2, BlockToolType::AXE));
    }
}