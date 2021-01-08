<?php

namespace CLADevs\VanillaX\entities\types;

use pocketmine\entity\Living;

class PigEntity extends Living{

    public $width = 0.9;
    public $height = 0.9;

    const NETWORK_ID = self::PIG;

    public function getName(): string{
        return "Pig";
    }
}