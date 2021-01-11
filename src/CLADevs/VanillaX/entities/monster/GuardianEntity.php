<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;

class GuardianEntity extends Living{

    public $width = 0.85;
    public $height = 0.85;

    const NETWORK_ID = self::GUARDIAN;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(30);
    }

    public function getName(): string{
        return "Guardian";
    }
}