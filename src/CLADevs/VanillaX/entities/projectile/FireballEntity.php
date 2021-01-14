<?php

namespace CLADevs\VanillaX\entities\projectile;

use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\level\Explosion;
use pocketmine\level\Position;

class FireballEntity extends Projectile{

    public $width = 0.31;
    public $height = 0.31;

    const NETWORK_ID = self::FIREBALL;

    protected function onHit(ProjectileHitEvent $event): void{
        $exp = new Explosion(Position::fromObject($this->add(0, $this->height / 2, 0), $this->level), 1, $this);
        $exp->explodeA();
        $exp->explodeB();
        if(!$this->isFlaggedForDespawn()) $this->flagForDespawn();
        //TODO set on fire
    }
}