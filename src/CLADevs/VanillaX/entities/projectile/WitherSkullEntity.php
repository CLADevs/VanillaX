<?php

namespace CLADevs\VanillaX\entities\projectile;

use pocketmine\entity\Entity;

class WitherSkullEntity extends Entity{

    public $width = 0.15;
    public $height = 0.15;

    const NETWORK_ID = self::WITHER_SKULL;
}