<?php

namespace CLADevs\VanillaX\entities\object;

use CLADevs\VanillaX\entities\Entity;

class ArmorStandEntity extends Entity{

    public $width = 0.5;
    public $height = 1.975;

    const NETWORK_ID = self::ARMOR_STAND;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(6);
    }
}