<?php

namespace CLADevs\VanillaX\entities\object;

use pocketmine\entity\Entity;

class EnderEyeEntity extends Entity{

    public $width = 0.25;
    public $height = 0.25;

    const NETWORK_ID = self::EYE_OF_ENDER_SIGNAL;
}