<?php

namespace CLADevs\VanillaX\entities\object;

use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class AreaEffectCloudEntity extends Entity{

    const NETWORK_ID = EntityIds::AREA_EFFECT_CLOUD;

    public float $width = 3;
    public float $height = 1;

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