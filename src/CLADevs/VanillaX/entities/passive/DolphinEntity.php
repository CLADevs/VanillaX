<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class DolphinEntity extends Living{

    public $width = 0.9;
    public $height = 0.6;

    const NETWORK_ID = self::DOLPHIN;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Dolphin";
    }
}