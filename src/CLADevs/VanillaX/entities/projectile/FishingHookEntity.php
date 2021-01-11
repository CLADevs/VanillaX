<?php

namespace CLADevs\VanillaX\entities\projectile;

use CLADevs\VanillaX\entities\Entity;

class FishingHookEntity extends Entity{

    public $width = 0.15;
    public $height = 0.15;

    const NETWORK_ID = self::FISHING_HOOK;
}