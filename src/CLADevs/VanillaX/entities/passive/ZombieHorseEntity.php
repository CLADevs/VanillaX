<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class ZombieHorseEntity extends VanillaEntity{

    const NETWORK_ID = self::ZOMBIE_HORSE;

    public $width = 1.4;
    public $height = 1.6;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(15);
    }

    public function getName(): string{
        return "Zombie Horse";
    }
}