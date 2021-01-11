<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\LivingEntity;

class EvocationIllagerEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::EVOCATION_ILLAGER;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(24);
    }

    public function getName(): string{
        return "Evocation Illager";
    }
}