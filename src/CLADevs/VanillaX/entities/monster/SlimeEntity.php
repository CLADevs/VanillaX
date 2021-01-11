<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;

class SlimeEntity extends Living{

    public $width = 2.08;
    public $height = 2.08;

    const NETWORK_ID = self::SLIME;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(16);
    }

    public function getName(): string{
        return "Slime";
    }
}