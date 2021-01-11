<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class SheepEntity extends Living{

    public $width = 0.9;
    public $height = 1.3;

    const NETWORK_ID = self::SHEEP;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(8);
    }

    public function getName(): string{
        return "Sheep";
    }
}