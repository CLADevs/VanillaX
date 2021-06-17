<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class FoxEntity extends VanillaEntity{

    const NETWORK_ID = self::FOX;

    public $width = 0.6;
    public $height = 0.7;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Fox";
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }
}