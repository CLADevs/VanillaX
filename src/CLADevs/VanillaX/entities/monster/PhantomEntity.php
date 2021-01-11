<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;

class PhantomEntity extends Living{

    public $width = 0.9;
    public $height = 0.5;

    const NETWORK_ID = self::PHANTOM;

    public function getName(): string{
        return "Phantom";
    }
}