<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;

class ZombieHorseEntity extends LivingEntity{

    public $width = 1.4;
    public $height = 1.6;

    const NETWORK_ID = self::ZOMBIE_HORSE;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(15);
    }

    public function getName(): string{
        return "Zombie Horse";
    }
}