<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class HorseEntity extends Living{

    public $width = 1.4;
    public $height = 1.6;

    const NETWORK_ID = self::HORSE;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(30);
    }

    public function getName(): string{
        return "Horse";
    }
}