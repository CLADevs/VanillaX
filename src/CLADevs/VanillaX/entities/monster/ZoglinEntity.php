<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\LivingEntity;

class ZoglinEntity extends LivingEntity{

    public $width = 0.9;
    public $height = 0.9;

    const NETWORK_ID = self::ZOGLIN;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(40);
    }

    public function getName(): string{
        return "Zoglin";
    }
}