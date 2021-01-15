<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\utils\EntityAgeable;

class StriderEntity extends LivingEntity{

    public $width = 0.9;
    public $height = 1.7;

    const NETWORK_ID = self::STRIDER;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.45, 0.85], [0.9, 1.7]);
        //TODO item Warped fungus
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Strider";
    }
}