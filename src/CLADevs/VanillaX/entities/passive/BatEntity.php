<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class BatEntity extends VanillaEntity{

    const NETWORK_ID = self::BAT;

    public $width = 0.5;
    public $height = 0.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(6);
        $this->setHealth(6);
    }

    public function getName(): string{
        return "Bat";
    }
}