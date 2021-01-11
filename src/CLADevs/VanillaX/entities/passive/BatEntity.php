<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;

class BatEntity extends LivingEntity{

    public $width = 0.5;
    public $height = 0.9;

    const NETWORK_ID = self::BAT;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(6);
    }

    public function getName(): string{
        return "Bat";
    }
}