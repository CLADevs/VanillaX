<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class SnowGolemEntity extends VanillaEntity{

    const NETWORK_ID = self::SNOW_GOLEM;

    public $width = 0.4;
    public $height = 1.8;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(4);
        $this->setHealth(4);
    }

    public function getName(): string{
        return "Snow Golem";
    }
}