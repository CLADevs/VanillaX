<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;

class VexEntity extends VanillaEntity{

    const NETWORK_ID = self::VEX;

    public $width = 0.4;
    public $height = 0.8;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(14);
    }

    public function getName(): string{
        return "Vex";
    }
}