<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\VanillaEntity;

class EndermanEntity extends VanillaEntity{

    const NETWORK_ID = self::ENDERMAN;

    public $width = 0.6;
    public $height = 2.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(40);
        $this->setHealth(40);
    }

    public function getName(): string{
        return "Enderman";
    }
}