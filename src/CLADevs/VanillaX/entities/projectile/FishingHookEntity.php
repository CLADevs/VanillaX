<?php

namespace CLADevs\VanillaX\entities\projectile;

use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class FishingHookEntity extends Entity{

    public float $width = 0.15;
    public float $height = 0.15;

    const NETWORK_ID = EntityIds::FISHING_HOOK;

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
}