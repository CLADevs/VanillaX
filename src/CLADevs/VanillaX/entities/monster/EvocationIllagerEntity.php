<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;

class EvocationIllagerEntity extends VanillaEntity{

    const NETWORK_ID = self::EVOCATION_ILLAGER;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(24);
    }

    public function getName(): string{
        return "Evocation Illager";
    }
}