<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class WolfEntity extends Living{

    public $width = 0.6;
    public $height = 0.85;

    const NETWORK_ID = self::WOLF;

    public function getName(): string{
        return "Wolf";
    }
}