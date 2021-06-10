<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class PigEntity extends VanillaEntity{

    const NETWORK_ID = self::PIG;

    public $width = 0.9;
    public $height = 0.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Pig";
    }
}