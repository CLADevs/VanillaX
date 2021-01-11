<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;

class ZombiePigmanEntity extends Living{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::ZOMBIE_PIGMAN;

    public function getName(): string{
        return "Zombie Pigman";
    }
}