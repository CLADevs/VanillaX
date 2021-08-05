<?php

namespace CLADevs\VanillaX\blocks\block\campfire;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;

class SoulCampfireBlock extends Opaque{

    //TODO functionality
    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::SOUL_CAMPFIRE, 0, ItemIdentifiers::SOUL_CAMPFIRE), "Soul Campfire", new BlockBreakInfo(2, BlockToolType::AXE, 0, 2));
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}