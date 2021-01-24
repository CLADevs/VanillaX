<?php

namespace CLADevs\VanillaX\blocks\types\chorus;

use pocketmine\block\Transparent;

class ChorusFlower extends Transparent{

    public function __construct(int $meta = 0){
        parent::__construct(self::CHORUS_FLOWER, $meta, "Chorus Flower");
    }

    public function getHardness(): float{
        return 0.4;
    }
}