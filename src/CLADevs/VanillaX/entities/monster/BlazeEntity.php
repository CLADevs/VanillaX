<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;

class BlazeEntity extends VanillaEntity{

    const NETWORK_ID = self::BLAZE;

    public $width = 0.5;
    public $height = 1.8;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
        $this->setHealth(20);
    }

    public function getName(): string{
        return "Blaze";
    }
}