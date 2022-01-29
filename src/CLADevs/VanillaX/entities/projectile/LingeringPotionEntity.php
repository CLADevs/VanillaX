<?php

namespace CLADevs\VanillaX\entities\projectile;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\projectile\Throwable;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class LingeringPotionEntity extends Throwable{

    const NETWORK_ID = EntityIds::LINGERING_POTION;

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.1, 0.1);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }

    public static function canRegister(): bool{
        return true;
    }
}