<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class TurtleEntity extends Living{

    public $width = 1.2;
    public $height = 0.4;

    const NETWORK_ID = self::TURTLE;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(30);
    }

    public function getName(): string{
        return "Turtle";
    }
}