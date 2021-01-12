<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\LivingEntity;

class WitherSkeletonEntity extends LivingEntity{

    public $width = 0.72;
    public $height = 2.01;

    const NETWORK_ID = self::WITHER_SKELETON;

    public function getName(): string{
        return "Wither Skeleton";
    }
}