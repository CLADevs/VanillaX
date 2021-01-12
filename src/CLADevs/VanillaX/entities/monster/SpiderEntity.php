<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\LivingEntity;

class SpiderEntity extends LivingEntity{

    public $width = 1.4;
    public $height = 0.9;

    const NETWORK_ID = self::SPIDER;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(16);
    }

    public function getName(): string{
        return "Spider";
    }
}