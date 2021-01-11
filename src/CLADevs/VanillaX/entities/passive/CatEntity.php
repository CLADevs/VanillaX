<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class CatEntity extends Living{

    public $width = 0.6;
    public $height = 0.7;

    const NETWORK_ID = self::CAT;

    public function getName(): string{
        return "Cat";
    }
}