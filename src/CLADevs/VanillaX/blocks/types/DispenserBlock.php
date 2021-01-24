<?php

namespace CLADevs\VanillaX\blocks\types;

use pocketmine\block\BlockIds;
use pocketmine\block\Solid;

class DispenserBlock extends Solid{

    public function __construct(int $meta = 0){
        parent::__construct(BlockIds::DISPENSER, $meta);
    }

    public function getHardness(): float{
        return 3.5;
    }
}