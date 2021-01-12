<?php

namespace CLADevs\VanillaX\entities\object;

use pocketmine\entity\Entity;

class EnderCrystalEntity extends Entity{

    public $width = 0.98;
    public $height = 0.98;

    const NETWORK_ID = self::ENDER_CRYSTAL;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(1);
    }
}