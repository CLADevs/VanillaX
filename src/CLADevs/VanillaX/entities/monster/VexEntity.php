<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\LivingEntity;

class VexEntity extends LivingEntity{

    public $width = 0.4;
    public $height = 0.8;

    const NETWORK_ID = self::VEX;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(14);
    }

    public function getName(): string{
        return "Vex";
    }
}