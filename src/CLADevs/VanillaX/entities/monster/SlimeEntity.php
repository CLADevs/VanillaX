<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\LivingEntity;

class SlimeEntity extends LivingEntity{

    public $width = 2.08;
    public $height = 2.08;

    const NETWORK_ID = self::SLIME;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(16);
    }

    public function getName(): string{
        return "Slime";
    }
}