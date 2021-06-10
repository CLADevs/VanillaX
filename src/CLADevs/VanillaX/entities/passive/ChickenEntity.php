<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class ChickenEntity extends VanillaEntity{

    const NETWORK_ID = self::CHICKEN;

    public $width = 0.6;
    public $height = 0.8;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(4);
    }

    public function getName(): string{
        return "Chicken";
    }
}