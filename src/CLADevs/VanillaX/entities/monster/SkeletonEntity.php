<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;

class SkeletonEntity extends Living{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::SKELETON;

    public function getName(): string{
        return "Skeleton";
    }
}