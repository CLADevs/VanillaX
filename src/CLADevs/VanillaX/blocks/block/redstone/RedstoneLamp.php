<?php

namespace CLADevs\VanillaX\blocks\block\redstone;

use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifierFlattened;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\RedstoneLamp as PMRedstoneLamp;

class RedstoneLamp extends PMRedstoneLamp implements NonAutomaticCallItemTrait{

    public function __construct(){
        parent::__construct(new BlockIdentifierFlattened(BlockLegacyIds::REDSTONE_LAMP, [BlockLegacyIds::LIT_REDSTONE_LAMP], 0), "Redstone Lamp", new BlockBreakInfo(0.3));
    }
}