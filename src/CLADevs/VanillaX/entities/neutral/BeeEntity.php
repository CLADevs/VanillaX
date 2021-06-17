<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\utils\interferces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;

class BeeEntity extends VanillaEntity{

    const NETWORK_ID = self::BEE;

    public $width = 0.55;
    public $height = 0.5;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Bee";
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::ARTHROPODS;
    }
}