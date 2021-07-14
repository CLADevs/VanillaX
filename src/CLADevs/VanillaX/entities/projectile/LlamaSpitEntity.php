<?php

namespace CLADevs\VanillaX\entities\projectile;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\projectile\Projectile;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class LlamaSpitEntity extends Projectile{

    public float $width = 0.31;
    public float $height = 0.31;

    /** @var float */
    protected $gravity = 0.06;

    const NETWORK_ID = EntityIds::LLAMA_SPIT;

    protected $damage = 1;

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
}