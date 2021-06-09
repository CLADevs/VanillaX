<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class StriderEntity extends VanillaEntity{

    const NETWORK_ID = self::STRIDER;

    public $width = 0.9;
    public $height = 1.7;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
        $this->setHealth(20);
    }

    public function getName(): string{
        return "Strider";
    }
}