<?php

namespace CLADevs\VanillaX\entities\object;

use pocketmine\entity\Entity;

class BoatEntity extends Entity{

    public $width = 1.4;
    public $height = 0.455;

    const NETWORK_ID = self::BOAT;
}