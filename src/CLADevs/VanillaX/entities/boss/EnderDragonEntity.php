<?php

namespace CLADevs\VanillaX\entities\boss;

use CLADevs\VanillaX\entities\VanillaEntity;

class EnderDragonEntity extends VanillaEntity{

    const NETWORK_ID = self::ENDER_DRAGON;

    public $width = 13;
    public $height = 4;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(200);
    }

    public function getName(): string{
        return "Ender Dragon";
    }
}