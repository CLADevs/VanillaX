<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;
use pocketmine\item\ToolTier;

class AncientDebrisBlock extends Opaque{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::ANCIENT_DEBRIS, 0, ItemIdentifiers::ANCIENT_DEBRIS), "Ancient", new BlockBreakInfo(5.0, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel(), 6000.0));
    }
}