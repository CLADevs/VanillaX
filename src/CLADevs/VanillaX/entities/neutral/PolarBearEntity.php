<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\utils\EntityAgeable;

class PolarBearEntity extends LivingEntity{

    public $width = 1.3;
    public $height = 1.4;

    const NETWORK_ID = self::POLAR_BEAR;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.65, 0.7], [1.3, 1.4]);
        $this->ageable->setCanBeBredByPlayer(false);
        $this->setMaxHealth(30);
    }

    public function getName(): string{
        return "Polar Bear";
    }
}