<?php

namespace CLADevs\VanillaX\blocks\block\fungus;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\Transparent;

class WarpedFungusBlock extends Transparent{

    //TODO functionality
    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::WARPED_FUNGUS, 0, ItemIdentifiers::WARPED_FUNGUS), "Warped Fungus", new BlockBreakInfo(0));
    }
}