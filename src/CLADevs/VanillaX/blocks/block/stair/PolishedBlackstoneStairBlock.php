<?php

namespace CLADevs\VanillaX\blocks\block\stair;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Stair;

class PolishedBlackstoneStairBlock extends Stair{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::POLISHED_BLACKSTONE_STAIRS, 0, ItemIdentifiers::POLISHED_BLACKSTONE_STAIRS), "Polished Blackstone Stairs", new BlockBreakInfo(3, BlockToolType::AXE, 0, 6));
    }
}