<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\LivingEntity;

class EndermanEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 2.9;

    const NETWORK_ID = self::ENDERMAN;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(40);
    }

    public function getName(): string{
        return "Enderman";
    }
}