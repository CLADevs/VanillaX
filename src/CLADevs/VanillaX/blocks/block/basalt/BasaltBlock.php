<?php

namespace CLADevs\VanillaX\blocks\block\basalt;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;

class BasaltBlock extends Opaque{

    //TODO facing
    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::BASALT, 0, ItemIdentifiers::BASALT), "Basalt", new BlockBreakInfo(1.25, BlockToolType::PICKAXE, 0, 4.2));
    }
}