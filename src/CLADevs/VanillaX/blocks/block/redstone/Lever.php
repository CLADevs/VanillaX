<?php

namespace CLADevs\VanillaX\blocks\block\redstone;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Lever as PMLever;

class Lever extends PMLever{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::LEVER, 0), "Lever", new BlockBreakInfo(0.5));
    }
}