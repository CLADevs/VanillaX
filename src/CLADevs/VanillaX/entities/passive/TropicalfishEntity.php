<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class TropicalfishEntity extends VanillaEntity{

    const NETWORK_ID = self::TROPICAL_FISH;

    public $width = 0.4;
    public $height = 0.4;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(6);
    }

    public function getName(): string{
        return "Tropical Fish";
    }
}