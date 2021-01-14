<?php

namespace CLADevs\VanillaX\entities\projectile;

use pocketmine\entity\projectile\Projectile;

class LlamaSpitEntity extends Projectile{

    public $width = 0.31;
    public $height = 0.31;

    protected $gravity = 0.06;

    const NETWORK_ID = self::LLAMA_SPIT;

    protected $damage = 1;
}