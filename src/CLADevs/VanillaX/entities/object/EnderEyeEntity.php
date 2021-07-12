<?php

namespace CLADevs\VanillaX\entities\object;

use pocketmine\block\Block;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\projectile\Throwable;
use pocketmine\math\RayTraceResult;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class EnderEyeEntity extends Throwable{

    public float $width = 0.25;
    public float $height = 0.25;

    const NETWORK_ID = EntityIds::EYE_OF_ENDER_SIGNAL;

    //TODO Timer for despawn
    protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult): void{
        parent::onHitBlock($blockHit, $hitResult);
        $this->flagForDespawn();
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
}