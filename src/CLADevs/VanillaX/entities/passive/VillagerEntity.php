<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\traits\EntityAgeable;

class VillagerEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::VILLAGER;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.3, 0.95], [0.6, 1.9]);
        //TODO cannot be breeded if its zombie villager
    }

    public function getName(): string{
        return "Villager";
    }
}