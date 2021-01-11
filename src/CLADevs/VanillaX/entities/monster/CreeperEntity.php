<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;

class CreeperEntity extends Living{

    public $width = 0.6;
    public $height = 1.8;

    const NETWORK_ID = self::CREEPER;

    public function getName(): string{
        return "Creeper";
    }
}