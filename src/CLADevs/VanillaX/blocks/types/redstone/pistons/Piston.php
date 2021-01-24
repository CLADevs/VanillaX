<?php

namespace CLADevs\VanillaX\blocks\types\redstone\pistons;

use pocketmine\block\Solid;

class Piston extends Solid{

    public function __construct(int $meta = 0){
        parent::__construct(self::PISTON, $meta, "Piston");
    }

    public function getHardness(): float{
        return 1.5;
    }

    public function getBlastResistance(): float{
        return 0.5;
    }
}