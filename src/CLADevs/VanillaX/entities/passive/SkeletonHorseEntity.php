<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;

class SkeletonHorseEntity extends VanillaEntity{

    const NETWORK_ID = self::SKELETON_HORSE;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(15);
    }

    public function getName(): string{
        return "Skeleton Horse";
    }
}