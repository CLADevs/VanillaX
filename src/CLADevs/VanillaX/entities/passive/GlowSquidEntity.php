<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class GlowSquidEntity extends VanillaEntity{

    const NETWORK_ID = self::GLOW_SQUID;

    public $width = 0.95;
    public $height = 0.95;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Glow_Squid";
    }
}