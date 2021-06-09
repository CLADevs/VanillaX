<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class TurtleEntity extends VanillaEntity{

    const NETWORK_ID = self::TURTLE;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(30);
        $this->setHealth(30);
    }

    public function getName(): string{
        return "Turtle";
    }
}