<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;

class ZombieVillagerEntity extends VanillaEntity{

    const NETWORK_ID = self::ZOMBIE_VILLAGER;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
        $this->setHealth(20);
    }

    public function getName(): string{
        return "Zombie Villager";
    }
}