<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\utils\EntityAgeable;

class TurtleEntity extends LivingEntity{

    public $width = 1.2;
    public $height = 0.4;

    const NETWORK_ID = self::TURTLE;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.096, 0.032], [1.2, 0.4]);
        //TODO item Seagrass
        $this->setMaxHealth(30);
    }

    public function getName(): string{
        return "Turtle";
    }
}