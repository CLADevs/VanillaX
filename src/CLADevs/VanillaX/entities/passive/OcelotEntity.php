<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;

class OcelotEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 0.7;

    const NETWORK_ID = self::OCELOT;

    public function getName(): string{
        return "Ocelot";
    }
}