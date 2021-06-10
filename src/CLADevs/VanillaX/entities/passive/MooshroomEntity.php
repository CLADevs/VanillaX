<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class MooshroomEntity extends VanillaEntity{

    const NETWORK_ID = self::MOOSHROOM;

    public $width = 0.9;
    public $height = 1.3;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Mooshroom";
    }
}