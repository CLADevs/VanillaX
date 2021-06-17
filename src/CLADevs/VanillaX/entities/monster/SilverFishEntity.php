<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\interferces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;

class SilverFishEntity extends VanillaEntity{

    const NETWORK_ID = self::SILVERFISH;

    public $width = 0.4;
    public $height = 0.3;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(8);
    }

    public function getName(): string{
        return "Silverfish";
    }

    public function getClassification(): int{
        return EntityClassification::ARTHROPODS;
    }
}