<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;

class TropicalFishEntity extends LivingEntity{

    public $width = 0.4;
    public $height = 0.4;

    const NETWORK_ID = self::TROPICAL_FISH;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(6);
    }

    public function getName(): string{
        return "TropicalFish";
    }
}