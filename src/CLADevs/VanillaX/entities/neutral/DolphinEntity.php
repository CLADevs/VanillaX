<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\VanillaEntity;

class DolphinEntity extends VanillaEntity{

    const NETWORK_ID = self::DOLPHIN;

    public $width = 0.9;
    public $height = 0.6;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Dolphin";
    }
}