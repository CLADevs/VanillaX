<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\traits\EntityAgeable;

class PandaEntity extends LivingEntity{

    public $width = 1.7;
    public $height = 1.5;

    const NETWORK_ID = self::PANDA;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.68, 0.6], [1.7, 1.5]);
        //TODO Bamboo Item
    }

    public function getName(): string{
        return "Panda";
    }
}