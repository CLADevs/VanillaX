<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;

class HuskEntity extends VanillaEntity{

    const NETWORK_ID = self::HUSK;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Husk";
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 + (count($this->getArmorInventory()->getContents()) * mt_rand(1,3)) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::UNDEAD;
    }
}