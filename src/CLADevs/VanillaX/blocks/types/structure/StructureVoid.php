<?php

namespace CLADevs\VanillaX\blocks\types\structure;

use CLADevs\VanillaX\blocks\BlockIdentifiers;
use CLADevs\VanillaX\items\utils\NonCreativeItemTrait;
use pocketmine\block\Transparent;

class StructureVoid extends Transparent implements NonCreativeItemTrait{

    public function __construct(int $meta = 0){
        parent::__construct(BlockIdentifiers::STRUCTURE_VOID, $meta, "Structure Void");
    }
}