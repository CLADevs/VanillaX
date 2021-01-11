<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class RabbitEntity extends Living{

    public $width = 0.67;
    public $height = 0.67;

    const NETWORK_ID = self::RABBIT;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(3);
    }

    public function getName(): string{
        return "Rabbit";
    }
}