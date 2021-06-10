<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class SheepEntity extends VanillaEntity{

    const NETWORK_ID = self::SHEEP;

    public $width = 0.9;
    public $height = 1.3;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(8);
    }

    public function getName(): string{
        return "Sheep";
    }
}