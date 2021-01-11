<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;

class SnowGolemEntity extends LivingEntity{

    public $width = 0.4;
    public $height = 1.8;

    const NETWORK_ID = self::SNOW_GOLEM;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(4);
    }

    public function getName(): string{
        return "Snow Golem";
    }
}