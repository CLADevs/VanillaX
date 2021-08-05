<?php

namespace CLADevs\VanillaX\blocks\block\root;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\Transparent;

class CrimsonRootBlock extends Transparent{

    //TODO functionality
    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::CRIMSON_ROOTS, 0, ItemIdentifiers::CRIMSON_ROOTS), "Crimson Root", new BlockBreakInfo(0));
    }
}