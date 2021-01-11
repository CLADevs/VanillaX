<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class OcelotEntity extends Living{

    public $width = 0.6;
    public $height = 0.7;

    const NETWORK_ID = self::OCELOT;

    public function getName(): string{
        return "Ocelot";
    }
}