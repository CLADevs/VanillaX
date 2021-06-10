<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;

class CreeperEntity extends VanillaEntity{

    const NETWORK_ID = self::CREEPER;

    public $width = 0.6;
    public $height = 1.8;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
    }

    public function isCharged(): bool{
        return $this->getGenericFlag(self::DATA_FLAG_POWERED);
    }

    public function getName(): string{
        return "Creeper";
    }
}