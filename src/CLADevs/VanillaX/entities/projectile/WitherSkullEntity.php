<?php

namespace CLADevs\VanillaX\entities\projectile;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\math\RayTraceResult;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\Server;
use pocketmine\world\Explosion;

class WitherSkullEntity extends Projectile{

    public float $width = 0.15;
    public float $height = 0.15;

    const NETWORK_ID = EntityIds::WITHER_SKULL;

    /** @var float */
    protected $gravity = 0.00;

    protected function onHit(ProjectileHitEvent $event): void{
        $pos = $this->getPosition();
        $pos->y += $this->height / 2;
        $exp = new Explosion($pos, 1, $this);
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
            $entityHit->getEffects()->add(new EffectInstance(VanillaEffects::WITHER(), $duration, 1));
        }
        parent::onHitEntity($entityHit, $hitResult);
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
}