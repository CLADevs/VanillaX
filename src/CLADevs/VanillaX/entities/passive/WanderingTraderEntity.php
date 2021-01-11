<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;

class WanderingTraderEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::WANDERING_TRADER;

    public function getName(): string{
        return "Wandering Trader";
    }
}