<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class ChickenEntity extends Living{

    public $width = 0.6;
    public $height = 0.8;

    const NETWORK_ID = self::CHICKEN;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(5);
    }

    public function getName(): string{
        return "Chicken";
    }
}