<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class MooshroomEntity extends Living{

    public $width = 0.9;
    public $height = 1.3;

    const NETWORK_ID = self::MOOSHROOM;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Mooshroom";
    }
}