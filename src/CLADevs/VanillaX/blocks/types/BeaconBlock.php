<?php

namespace CLADevs\VanillaX\blocks\types;

use pocketmine\block\Transparent;

class BeaconBlock extends Transparent{

    public function __construct(int $meta = 0){
        parent::__construct(self::BEACON, $meta, "Beacon");
    }

    public function getHardness(): float{
        return 3;
    }

    public function getFlammability(): int{
        return 15;
    }
}