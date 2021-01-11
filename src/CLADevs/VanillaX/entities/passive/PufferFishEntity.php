<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class PufferFishEntity extends Living{

    public $width = 0.8;
    public $height = 0.8;

    const NETWORK_ID = self::PUFFERFISH;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(6);
    }

    public function getName(): string{
        return "PufferFish";
    }
}