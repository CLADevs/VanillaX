<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class CowEntity extends VanillaEntity{

    const NETWORK_ID = self::COW;

    public $width = 0.9;
    public $height = 1.3;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Cow";
    }
}