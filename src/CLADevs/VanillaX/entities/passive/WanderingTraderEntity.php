<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class WanderingTraderEntity extends VanillaEntity{

    const NETWORK_ID = self::WANDERING_TRADER;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
        $this->setHealth(20);
    }

    public function getName(): string{
        return "Wandering Trader";
    }
}