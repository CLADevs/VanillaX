<?php

namespace CLADevs\VanillaX\blocks\block\redstone;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\RedstoneWire as PMRedstoneWire;
use pocketmine\item\ItemIds;

class RedstoneWire extends PMRedstoneWire{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::REDSTONE_WIRE, 0, ItemIds::REDSTONE), "Redstone", BlockBreakInfo::instant());
    }
}