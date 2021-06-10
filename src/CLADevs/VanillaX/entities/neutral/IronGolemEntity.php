<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\VanillaEntity;

class IronGolemEntity extends VanillaEntity{

    const NETWORK_ID = self::IRON_GOLEM;

    public $width = 1.4;
    public $height = 2.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(100);
    }

    public function getName(): string{
        return "Iron Golem";
    }
}