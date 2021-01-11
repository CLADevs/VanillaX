<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;

class VexEntity extends Living{

    public $width = 0.4;
    public $height = 0.8;

    const NETWORK_ID = self::VEX;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(14);
    }

    public function getName(): string{
        return "Vex";
    }
}