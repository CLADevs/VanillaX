<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;

class PiglinBruteEntity extends VanillaEntity{

    const NETWORK_ID = self::PIGLIN_BRUTE;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(50);
    }

    public function getName(): string{
        return "Piglin Brute";
    }
}