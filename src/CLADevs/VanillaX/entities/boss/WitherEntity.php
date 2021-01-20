<?php

namespace CLADevs\VanillaX\entities\boss;

use CLADevs\VanillaX\entities\LivingEntity;

class WitherEntity extends LivingEntity{

    public $width = 1;
    public $height = 3;

    const NETWORK_ID = self::WITHER;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(600);
    }

    public function getName(): string{
        return "Wither";
    }
}