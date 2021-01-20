<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\LivingEntity;

class ZombiePigmanEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::ZOMBIE_PIGMAN;

    public function getName(): string{
        return "Zombie Pigman";
    }
}