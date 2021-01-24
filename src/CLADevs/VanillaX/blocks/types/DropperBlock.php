<?php

namespace CLADevs\VanillaX\blocks\types;

use pocketmine\block\BlockIds;
use pocketmine\block\Solid;

class DropperBlock extends Solid{

    public function __construct(int $meta = 0){
        parent::__construct(BlockIds::DROPPER, $meta);
    }

    public function getHardness(): float{
        return 3.5;
    }
}