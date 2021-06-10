<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;

class GuardianEntity extends VanillaEntity{

    const NETWORK_ID = self::GUARDIAN;

    public $width = 0.85;
    public $height = 0.85;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(30);
    }

    public function getName(): string{
        return "Guardian";
    }
}