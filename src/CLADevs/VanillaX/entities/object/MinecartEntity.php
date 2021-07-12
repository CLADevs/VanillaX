<?php

namespace CLADevs\VanillaX\entities\object;

use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class MinecartEntity extends Entity{

    public float $width = 0.98;
    public float $height = 0.7;

    const NETWORK_ID = EntityIds::MINECART;

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
}