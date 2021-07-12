<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\CauldronTile;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Transparent;
use pocketmine\item\ItemIds;

class CauldronBlock extends Transparent{

    const EMPTY_CAULDRON = 0;
    const LEVEL_START = 1;
    const LEVEL_START_2 = 2;
    const LEVEL_MIDDLE = 3;
    const LEVEL_MIDDLE_2 = 4;
    const LEVEL_NEARLY_FULL = 5;
    const LEVEL_FULL = 6;

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::CAULDRON_BLOCK, 0, ItemIds::CAULDRON, CauldronTile::class), "Cauldron", new BlockBreakInfo(2));
    }
}