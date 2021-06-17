<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class OcelotEntity extends VanillaEntity{

    const NETWORK_ID = self::OCELOT;

    public $width = 0.6;
    public $height = 0.7;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Ocelot";
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }
}