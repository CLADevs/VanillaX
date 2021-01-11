<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;

class DrownedEntity extends Living{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::BLAZE;

    public function getName(): string{
        return "Blaze";
    }
}