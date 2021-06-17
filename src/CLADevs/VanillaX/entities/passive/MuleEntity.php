<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class MuleEntity extends VanillaEntity{

    const NETWORK_ID = self::MULE;

    public $width = 1.4;
    public $height = 1.6;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setRangeHealth([15, 30]);
    }

    public function getName(): string{
        return "Mule";
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }
}