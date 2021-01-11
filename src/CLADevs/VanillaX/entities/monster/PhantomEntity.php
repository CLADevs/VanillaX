<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\LivingEntity;

class PhantomEntity extends LivingEntity{

    public $width = 0.9;
    public $height = 0.5;

    const NETWORK_ID = self::PHANTOM;

    public function getName(): string{
        return "Phantom";
    }
}