<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;

class MagmaCubeEntity extends Living{

    public $width = 2.08;
    public $height = 2.08;

    const NETWORK_ID = self::MAGMA_CUBE;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(16);
    }

    public function getName(): string{
        return "Magma Cube";
    }
}