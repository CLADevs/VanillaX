<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;

class RavagerEntity extends VanillaEntity{

    const NETWORK_ID = self::RAVAGER;

    public $width = 1.9;
    public $height = 1.2;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(100);
        $this->setHealth(100);
    }

    public function getName(): string{
        return "Ravager";
    }
}