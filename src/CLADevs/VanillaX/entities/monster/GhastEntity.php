<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;

class GhastEntity extends VanillaEntity{

    const NETWORK_ID = self::GHAST;

    public $width = 4;
    public $height = 4;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Ghast";
    }
}