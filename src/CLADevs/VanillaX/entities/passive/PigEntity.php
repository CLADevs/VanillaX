<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class PigEntity extends Living{

    public $width = 0.9;
    public $height = 0.9;

    const NETWORK_ID = self::PIG;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Pig";
    }
}