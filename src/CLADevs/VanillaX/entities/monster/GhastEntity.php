<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\LivingEntity;

class GhastEntity extends LivingEntity{

    public $width = 4;
    public $height = 4;

    const NETWORK_ID = self::GHAST;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Ghast";
    }
}