<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\utils\FacingPlayerHorizontallyTrait;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;
use pocketmine\item\ItemIds;

class BellBlock extends Opaque{
    use FacingPlayerHorizontallyTrait;

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::BELL, 0, ItemIds::BELL), "Bell", new BlockBreakInfo(5, BlockToolType::PICKAXE, 0, 5));
    }
}