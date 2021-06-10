<?php

namespace CLADevs\VanillaX\entities\boss;

use CLADevs\VanillaX\entities\VanillaEntity;

class WitherEntity extends VanillaEntity{

    const NETWORK_ID = self::WITHER;

    public $width = 1;
    public $height = 3;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(600);
    }

    public function getName(): string{
        return "Wither";
    }
}