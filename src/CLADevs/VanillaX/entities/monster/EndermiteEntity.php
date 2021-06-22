<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;

class EndermiteEntity extends VanillaEntity{

    const NETWORK_ID = self::ENDERMITE;

    public $width = 0.4;
    public $height = 0.3;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(8);
    }

    public function getName(): string{
        return "Endermite";
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 3 : 0;
    }

    public function getClassification(): int{
        return EntityClassification::ARTHROPODS;
    }
}