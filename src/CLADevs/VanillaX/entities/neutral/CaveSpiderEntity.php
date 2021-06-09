<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\VanillaEntity;

class CaveSpiderEntity extends VanillaEntity{

    const NETWORK_ID = self::CAVE_SPIDER;

    public $width = 0.7;
    public $height = 0.5;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(12);
        $this->setHealth(12);
    }

    public function getName(): string{
        return "Cave Spider";
    }
}