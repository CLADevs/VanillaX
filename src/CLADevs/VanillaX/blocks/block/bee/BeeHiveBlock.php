<?php

namespace CLADevs\VanillaX\blocks\block\bee;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;

class BeeHiveBlock extends Opaque{

    //TODO facing and actual bee thing LOL
    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::BEEHIVE, 0, ItemIdentifiers::BEEHIVE), "BeeHive", new BlockBreakInfo(0.6, BlockToolType::PICKAXE, 0, 0.6));
    }
}