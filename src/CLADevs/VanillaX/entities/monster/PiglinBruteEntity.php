<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\LivingEntity;

class PiglinBruteEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::PIGLIN_BRUTE;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(50);
    }

    public function getName(): string{
        return "Piglin Brute";
    }
}