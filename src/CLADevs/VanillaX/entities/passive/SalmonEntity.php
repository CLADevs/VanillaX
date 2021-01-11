<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class SalmonEntity extends Living{

    public $width = 0.5;
    public $height = 0.5;

    const NETWORK_ID = self::SALMON;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(6);
    }

    public function getName(): string{
        return "Salmon";
    }
}