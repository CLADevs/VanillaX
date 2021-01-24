<?php

namespace CLADevs\VanillaX\blocks\types\chorus;

use pocketmine\block\Transparent;

class ChorusPlant extends Transparent{

    public function __construct(int $meta = 0){
        parent::__construct(self::CHORUS_PLANT, $meta, "Chorus Plant");
    }

    public function getHardness(): float{
        return 0.4;
    }
}