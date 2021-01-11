<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;

class BeeEntity extends Living{

    public $width = 0.55;
    public $height = 0.5;

    const NETWORK_ID = 122; //BEE ID

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Bee";
    }
}