<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class VillagerEntity extends Living{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::VILLAGER;

    public function getName(): string{
        return "Villager";
    }
}