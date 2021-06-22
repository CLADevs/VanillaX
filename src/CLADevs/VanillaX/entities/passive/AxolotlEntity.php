<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;

class AxolotlEntity extends VanillaEntity{

    const NETWORK_ID = self::AXOLOTL;

    public $width = 1.3;
    public $height = 0.6;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(14);
    }

    public function getName(): string{
        return "Axolotl";
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::AQUATIC;
    }
}