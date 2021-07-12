<?php

namespace CLADevs\VanillaX\entities\projectile;

use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class DragonFireballEntity extends Entity{

    public float $width = 0.31;
    public float $height = 0.31;

    const NETWORK_ID = EntityIds::DRAGON_FIREBALL;

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
}