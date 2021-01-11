<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class IronGolemEntity extends Living{

    public $width = 1.4;
    public $height = 2.9;

    const NETWORK_ID = self::IRON_GOLEM;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(100);
    }

    public function getName(): string{
        return "Iron Golem";
    }
}