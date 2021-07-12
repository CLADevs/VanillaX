<?php

namespace CLADevs\VanillaX\blocks\block;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Sponge;

class SpongeBlock extends Sponge{
    //TODO drain the water

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::SPONGE, 0), "Sponge", new BlockBreakInfo(0.6, BlockToolType::HOE));
    }
}