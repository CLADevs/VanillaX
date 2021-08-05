<?php

namespace CLADevs\VanillaX\blocks\block\hyphae;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;

class CrimsonHyphaeBlock extends Opaque{

    //TODO functionality
    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::CRIMSON_HYPHAE, 0, ItemIdentifiers::CRIMSON_HYPHAE), "Crimson Hyphae", new BlockBreakInfo(2, BlockToolType::AXE));
    }
}