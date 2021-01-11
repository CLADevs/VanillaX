<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\LivingEntity;

class BlazeEntity extends LivingEntity{

    public $width = 0.5;
    public $height = 1.8;

    const NETWORK_ID = self::BLAZE;

    public function getName(): string{
        return "Blaze";
    }

}