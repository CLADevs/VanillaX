<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;

class HuskEntity extends Living{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::HUSK;

    public function getName(): string{
        return "Husk";
    }
}