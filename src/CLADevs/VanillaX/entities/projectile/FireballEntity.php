<?php

namespace CLADevs\VanillaX\entities\projectile;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\world\Explosion;

class FireballEntity extends Projectile{

    public float $width = 0.31;
    public float $height = 0.31;

    const NETWORK_ID = EntityIds::FIREBALL;

    protected function onHit(ProjectileHitEvent $event): void{
        $pos = $this->getPosition();
        $pos->y += $this->height / 2;
        $exp = new Explosion($pos, 1, $this);
        $exp->explodeA();
        $exp->explodeB();
        if(!$this->isFlaggedForDespawn()) $this->flagForDespawn();
        //TODO set on fire
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
}