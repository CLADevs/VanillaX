<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class ShulkerEntity extends Living{

    public $width = 1;
    public $height = 1;

    const NETWORK_ID = self::SHEEP;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(30);
    }

    public function getName(): string{
        return "Shulker";
    }
}