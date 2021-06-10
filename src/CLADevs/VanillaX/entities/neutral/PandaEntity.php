<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\VanillaEntity;

class PandaEntity extends VanillaEntity{

    const NETWORK_ID = self::PANDA;

    public $width = 1.7;
    public $height = 1.5;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Panda";
    }
}