<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\LivingEntity;

class CreeperEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.8;

    const NETWORK_ID = self::CREEPER;

    public function getName(): string{
        return "Creeper";
    }
}