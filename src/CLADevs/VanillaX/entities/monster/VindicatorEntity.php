<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;

class VindicatorEntity extends Living{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::VINDICATOR;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(24);
    }

    public function getName(): string{
        return "Vindicator";
    }
}