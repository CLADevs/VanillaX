<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class RabbitEntity extends VanillaEntity{

    const NETWORK_ID = self::RABBIT;

    public $width = 0.67;
    public $height = 0.67;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(3);
        $this->setHealth(3);
    }

    public function getName(): string{
        return "Rabbit";
    }
}