<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\VanillaEntity;

class PiglinEntity extends VanillaEntity{

    const NETWORK_ID = self::PIGLIN;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(16);
    }

    public function getName(): string{
        return "Piglin";
    }

    //TODO drops
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 + (count($this->getArmorInventory()->getContents()) * mt_rand(1,3)) : 0;
    }
}