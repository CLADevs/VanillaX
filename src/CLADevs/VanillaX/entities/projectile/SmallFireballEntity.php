<?php

namespace CLADevs\VanillaX\entities\projectile;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\projectile\Projectile;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class SmallFireballEntity extends Projectile{

    public float $width = 0.31;
    public float $height = 0.31;

    const NETWORK_ID = EntityIds::SMALL_FIREBALL;

    protected $damage = 5;

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
}