<?php

namespace CLADevs\VanillaX\entities\projectile;

use pocketmine\entity\projectile\Projectile;

class SmallFireballEntity extends Projectile{

    public $width = 0.31;
    public $height = 0.31;

    const NETWORK_ID = self::SMALL_FIREBALL;

    protected $damage = 5;
}