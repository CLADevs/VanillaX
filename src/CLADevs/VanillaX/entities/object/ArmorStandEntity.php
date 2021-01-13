<?php

namespace CLADevs\VanillaX\entities\object;

use pocketmine\entity\Entity;

class ArmorStandEntity extends Entity{

    public $width = 0.5;
    public $height = 1.975;
    protected $gravity = 0.5;

    const NETWORK_ID = self::ARMOR_STAND;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(6);
    }
}