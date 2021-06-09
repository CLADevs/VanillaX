<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class FishEntity extends VanillaEntity{

    const NETWORK_ID = self::FISH;

    public $width = 0.6;
    public $height = 0.3;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(6);
        $this->setHealth(6);
    }

    public function getName(): string{
        return "Fish";
    }
}