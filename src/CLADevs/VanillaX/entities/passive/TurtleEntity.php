<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;

class TurtleEntity extends VanillaEntity{

    const NETWORK_ID = self::TURTLE;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(30);
    }

    public function getName(): string{
        return "Turtle";
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::AQUATIC;
    }
}