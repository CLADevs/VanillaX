<?php

namespace CLADevs\VanillaX\blocks\block;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Skull;
use pocketmine\block\tile\Skull as TileSkull;

class SkullBlock extends Skull{

    //TODO make dragon head move with redstone

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::MOB_HEAD_BLOCK, 0, null, TileSkull::class), "Mob Head", new BlockBreakInfo(1.0));
    }
}