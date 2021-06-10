<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class ParrotEntity extends VanillaEntity{

    const NETWORK_ID = self::PARROT;

    public $width = 0.5;
    public $height = 1;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(6);
    }

    public function getName(): string{
        return "Parrot";
    }
}