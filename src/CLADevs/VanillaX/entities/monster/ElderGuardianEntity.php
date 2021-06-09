<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;

class ElderGuardianEntity extends VanillaEntity{

    const NETWORK_ID = self::ELDER_GUARDIAN;

    public $width = 1.99;
    public $height = 1.99;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(80);
        $this->setHealth(80);
    }

    public function getName(): string{
        return "Elder Guardian";
    }
}