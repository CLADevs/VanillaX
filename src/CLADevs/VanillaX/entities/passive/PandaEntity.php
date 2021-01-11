<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class PandaEntity extends Living{

    public $width = 1.7;
    public $height = 1.5;

    const NETWORK_ID = self::PANDA;

    public function getName(): string{
        return "Panda";
    }
}