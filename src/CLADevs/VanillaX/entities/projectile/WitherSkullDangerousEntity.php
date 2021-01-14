<?php

namespace CLADevs\VanillaX\entities\projectile;

use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\level\Explosion;
use pocketmine\level\Position;

class WitherSkullDangerousEntity extends WitherSkullEntity{

    const NETWORK_ID = self::WITHER_SKULL_DANGEROUS;

    protected function onHit(ProjectileHitEvent $event): void{
        $exp = new Explosion(Position::fromObject($this->add(0, $this->height / 2, 0), $this->level), 4, $this);
        $exp->explodeA();
        $exp->explodeB();
    }
}