<?php

namespace CLADevs\VanillaX\blocks\block\fungus;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\Transparent;

class CrimsonFungusBlock extends Transparent{

    //TODO functionality
    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::CRIMSON_FUNGUS, 0, ItemIdentifiers::CRIMSON_FUNGUS), "Crimson Fungus", new BlockBreakInfo(0));
    }
}