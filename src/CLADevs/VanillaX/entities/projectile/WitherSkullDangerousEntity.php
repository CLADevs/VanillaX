<?php

namespace CLADevs\VanillaX\entities\projectile;

use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\world\Explosion;

class WitherSkullDangerousEntity extends WitherSkullEntity{

    const NETWORK_ID = EntityIds::WITHER_SKULL_DANGEROUS;

    protected function onHit(ProjectileHitEvent $event): void{
        $pos = $this->getPosition();
        $pos->y += $this->height / 2;
        $exp = new Explosion($pos, 4, $this);
        $exp->explodeA();
        $exp->explodeB();
    }
}