<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\LivingEntity;

class CaveSpiderEntity extends LivingEntity{

    public $width = 0.7;
    public $height = 0.5;

    const NETWORK_ID = self::CAVE_SPIDER;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(12);
    }

    public function getName(): string{
        return "Cave Spider";
    }
}