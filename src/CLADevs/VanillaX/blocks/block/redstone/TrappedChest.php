<?php

namespace CLADevs\VanillaX\blocks\block\redstone;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\tile\Chest as TileChest;
use pocketmine\block\TrappedChest as PMTrappedChest;

class TrappedChest extends PMTrappedChest{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::TRAPPED_CHEST, 0, null, TileChest::class), "Trapped Chest", new BlockBreakInfo(2.5, BlockToolType::AXE));
    }
}