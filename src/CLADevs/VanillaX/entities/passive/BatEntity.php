<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class BatEntity extends Living{

    public $width = 0.5;
    public $height = 0.9;

    const NETWORK_ID = self::BAT;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(6);
    }

    public function getName(): string{
        return "Bat";
    }
}