<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;

class WitherSkeletonEntity extends Living{

    public $width = 0.72;
    public $height = 2.01;

    const NETWORK_ID = self::WITHER_SKELETON;

    public function getName(): string{
        return "Wither Skeleton";
    }
}