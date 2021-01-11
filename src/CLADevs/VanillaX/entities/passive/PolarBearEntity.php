<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class PolarBearEntity extends Living{

    public $width = 1.3;
    public $height = 1.4;

    const NETWORK_ID = self::POLAR_BEAR;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(30);
    }

    public function getName(): string{
        return "Polar Bear";
    }
}