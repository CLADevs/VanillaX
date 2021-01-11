<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;

class FishEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 0.3;

    const NETWORK_ID = self::FISH;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(6);
    }

    public function getName(): string{
        return "Fish";
    }
}