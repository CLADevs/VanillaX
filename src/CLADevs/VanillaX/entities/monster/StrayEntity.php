<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;

class StrayEntity extends Living{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::STRAY;

    public function getName(): string{
        return "Stray";
    }
}