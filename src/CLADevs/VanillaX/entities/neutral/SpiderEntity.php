<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\VanillaEntity;

class SpiderEntity extends VanillaEntity{

    const NETWORK_ID = self::SPIDER;

    public $width = 1.4;
    public $height = 0.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(16);
    }

    public function getName(): string{
        return "Spider";
    }
}