<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;

class EndermiteEntity extends Living{

    public $width = 0.4;
    public $height = 0.3;

    const NETWORK_ID = self::ENDERMITE;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(8);
    }

    public function getName(): string{
        return "Endermite";
    }
}