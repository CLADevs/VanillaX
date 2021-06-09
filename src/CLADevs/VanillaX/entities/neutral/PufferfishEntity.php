<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\VanillaEntity;

class PufferfishEntity extends VanillaEntity{

    const NETWORK_ID = self::PUFFERFISH;

    public $width = 0.8;
    public $height = 0.8;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(6);
        $this->setHealth(6);
    }

    public function getName(): string{
        return "Puffer Fish";
    }
}