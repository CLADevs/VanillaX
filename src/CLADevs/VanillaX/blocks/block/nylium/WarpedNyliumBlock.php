<?php

namespace CLADevs\VanillaX\blocks\block\nylium;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Planks;

class WarpedNyliumBlock extends Planks{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::WARPED_NYLIUM, 0, ItemIdentifiers::WARPED_NYLIUM), "Warped Nylium", new BlockBreakInfo(0.4, BlockToolType::PICKAXE, 0, 1));
    }
}