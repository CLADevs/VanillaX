<?php

namespace CLADevs\VanillaX\blocks\block\basalt;

use CLADevs\VanillaX\blocks\BlockIds;
use CLADevs\VanillaX\blocks\utils\BlockFacingOppositeTrait;
use CLADevs\VanillaX\items\LegacyItemIds;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;

class BasaltBlock extends Opaque{
    use BlockFacingOppositeTrait;

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockIds::BASALT, 0, LegacyItemIds::BASALT), "Basalt", new BlockBreakInfo(1.25, BlockToolType::PICKAXE, 0, 4.2));
    }
}