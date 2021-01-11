<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;

class WitchEntity extends Living{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::WITCH;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(26);
    }

    public function getName(): string{
        return "Witch";
    }
}