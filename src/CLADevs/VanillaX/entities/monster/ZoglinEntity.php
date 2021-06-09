<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;

class ZoglinEntity extends VanillaEntity{

    const NETWORK_ID = self::ZOGLIN;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(40);
        $this->setHealth(40);
    }

    public function getName(): string{
        return "Zoglin";
    }
}