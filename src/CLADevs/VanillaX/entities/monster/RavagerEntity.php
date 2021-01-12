<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\LivingEntity;

class RavagerEntity extends LivingEntity{

    public $width = 1.9;
    public $height = 1.2;

    const NETWORK_ID = self::RAVAGER;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(100);
    }

    public function getName(): string{
        return "Ravager";
    }
}