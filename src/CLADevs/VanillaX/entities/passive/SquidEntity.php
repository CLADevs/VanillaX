<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\traits\EntityAgeable;

class SquidEntity extends LivingEntity{

    public $width = 0.95;
    public $height = 0.95;

    const NETWORK_ID = self::SQUID;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.3, 0.95], [0.6, 1.9]);
        $this->ageable->setCanBeBredByPlayer(false);
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Squid";
    }
}