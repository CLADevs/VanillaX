<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Transparent;

class ShroomLight extends Transparent{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::SHROOMLIGHT, 0, ItemIdentifiers::SHROOMLIGHT), "Shroomlight", new BlockBreakInfo(1, BlockToolType::HOE, 0, 1));
    }
}
