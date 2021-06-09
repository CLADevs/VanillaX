<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class SquidEntity extends VanillaEntity{

    const NETWORK_ID = self::SQUID;

    public $width = 0.95;
    public $height = 0.95;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
        $this->setHealth(10);
    }

    public function getName(): string{
        return "Squid";
    }
}