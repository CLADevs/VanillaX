<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\LivingEntity;

class ElderGuardianEntity extends LivingEntity{

    public $width = 1.99;
    public $height = 1.99;

    const NETWORK_ID = self::ELDER_GUARDIAN;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(80);
    }

    public function getName(): string{
        return "Elder Guardian";
    }
}