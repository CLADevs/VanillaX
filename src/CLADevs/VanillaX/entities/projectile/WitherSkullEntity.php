<?php

namespace CLADevs\VanillaX\entities\projectile;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\level\Explosion;
use pocketmine\level\Position;
use pocketmine\math\RayTraceResult;
use pocketmine\Server;

class WitherSkullEntity extends Projectile{

    public $width = 0.15;
    public $height = 0.15;

    const NETWORK_ID = self::WITHER_SKULL;

    protected $gravity = 0.00;

    protected function onHit(ProjectileHitEvent $event): void{
        $exp = new Explosion(Position::fromObject($this->add(0, $this->height / 2, 0), $this->level), 1, $this);
        $exp->explodeA();
        $exp->explodeB();
    }

    protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult): void{
        if($entityHit instanceof Living){
            $duration = 0;

            if(($diff = Server::getInstance()->getDifficulty()) === 2){
                $duration = 200;
            }elseif($diff === 3){
                $duration = 800;
            }
            $entityHit->addEffect(new EffectInstance(Effect::getEffect(Effect::WITHER), $duration, 1));
        }
        parent::onHitEntity($entityHit, $hitResult);
    }
}