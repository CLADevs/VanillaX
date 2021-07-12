<?php

namespace CLADevs\VanillaX\blocks\block\structure;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\utils\item\NonCreativeItemTrait;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\Transparent;

class StructureVoid extends Transparent implements NonCreativeItemTrait{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::STRUCTURE_VOID, 0), "Structure Void", new BlockBreakInfo(0));
    }
}