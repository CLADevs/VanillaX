<?php

namespace CLADevs\VanillaX\blocks\block\redstone\buttons;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\StoneButton as PMStoneButton;

class CrimsonButton extends PMStoneButton{

    //TODO functionality
    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::CRIMSON_BUTTON, 0, ItemIdentifiers::CRIMSON_BUTTON), "Crimson Button", new BlockBreakInfo(0.5));
    }
}