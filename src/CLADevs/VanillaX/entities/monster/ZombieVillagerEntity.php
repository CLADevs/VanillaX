<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\LivingEntity;

class ZombieVillagerEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::ZOMBIE_VILLAGER;

    public function getName(): string{
        return "Zombie Villager";
    }
}