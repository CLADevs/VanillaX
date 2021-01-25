<?php

namespace CLADevs\VanillaX\entities\object;

use pocketmine\block\Block;
use pocketmine\entity\projectile\Throwable;
use pocketmine\math\RayTraceResult;

class EnderEyeEntity extends Throwable{

    public $width = 0.25;
    public $height = 0.25;

    const NETWORK_ID = self::EYE_OF_ENDER_SIGNAL;

    //TODO Timer for despawn
    protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult): void{
        parent::onHitBlock($blockHit, $hitResult);
        $this->flagForDespawn();
    }
}