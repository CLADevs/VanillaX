<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class ParrotEntity extends Living{

    public $width = 0.5;
    public $height = 1;

    const NETWORK_ID = self::PARROT;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(6);
    }

    public function getName(): string{
        return "Parrot";
    }
}