<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;

class WitherSkeletonEntity extends VanillaEntity{

    const NETWORK_ID = self::WITHER_SKELETON;

    public $width = 0.72;
    public $height = 2.01;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
        $this->setHealth(20);
    }

    public function getName(): string{
        return "Wither Skeleton";
    }
}