<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;

class WitchEntity extends VanillaEntity{

    const NETWORK_ID = self::WITCH;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(26);
        $this->setHealth(26);
    }

    public function getName(): string{
        return "Witch";
    }
}