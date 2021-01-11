<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;

class ZombieEntity extends Living{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::ZOMBIE;

    public function getName(): string{
        return "Zombie";
    }
}