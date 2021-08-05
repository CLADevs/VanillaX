<?php

namespace CLADevs\VanillaX\blocks\block\campfire;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;
use pocketmine\item\ItemIds;

class CampfireBlock extends Opaque{

    //TODO functionality
    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::CAMPFIRE, 0, ItemIds::CAMPFIRE), "Campfire", new BlockBreakInfo(2, BlockToolType::AXE, 0, 2));
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}