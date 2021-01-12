<?php

namespace CLADevs\VanillaX\entities\object;

use pocketmine\entity\Entity;

class LightningBoltEntity extends Entity{

    public $width = 1;
    public $height = 1;

    const NETWORK_ID = self::LIGHTNING_BOLT;
}