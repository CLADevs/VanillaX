<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;

class BlazeEntity extends Living{

    public $width = 0.5;
    public $height = 1.8;

    const NETWORK_ID = self::BLAZE;

    public function getName(): string{
        return "Blaze";
    }

}