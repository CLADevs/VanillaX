<?php

namespace CLADevs\VanillaX\blocks\block\structure;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\utils\item\NonCreativeItemTrait;
use pocketmine\block\Transparent;

class StructureVoid extends Transparent implements NonCreativeItemTrait{

    public function __construct(int $meta = 0){
        parent::__construct(BlockVanilla::STRUCTURE_VOID, $meta, "Structure Void");
    }
}