<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class DonkeyEntity extends VanillaEntity{

    const NETWORK_ID = self::DONKEY;

    public $width = 1.4;
    public $height = 1.6;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setRangeHealth([15, 30]);
    }

    public function getName(): string{
        return "Donkey";
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }
}