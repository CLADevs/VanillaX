<?php

namespace CLADevs\VanillaX\entities\projectile;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\projectile\Projectile;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class TridentEntity extends Projectile{

    public float $width = 0.25;
    public float $height = 0.35;

    /** @var float */
    protected $gravity = 0.05;
    /** @var float */
    protected $drag = 0.01;

    const NETWORK_ID = EntityIds::THROWN_TRIDENT;

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }

    public static function canRegister(): bool{
        return true;
    }
}