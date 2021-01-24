<?php

namespace CLADevs\VanillaX\blocks\types\redstone;

use pocketmine\block\Transparent;

class RedstoneWire extends Transparent{

    public function __construct(int $meta = 0){
        parent::__construct(self::REDSTONE_WIRE, $meta);
    }

    public function getHardness(): float{
        return 0;
    }
}