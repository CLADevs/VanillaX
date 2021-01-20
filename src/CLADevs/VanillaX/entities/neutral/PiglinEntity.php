<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\utils\EntityAgeable;

class PiglinEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::PIGLIN;

    protected function initEntity(): void{
        parent::initEntity();
        //TODO
        $this->ageable = new EntityAgeable($this, [0.6, 1.9], [0.6, 1.9]);
        $this->ageable->setCanBeBredByPlayer(false);
        $this->setMaxHealth(16);
    }

    public function getName(): string{
        return "Piglin";
    }
}