<?php

namespace CLADevs\VanillaX\entities\projectile;

use CLADevs\VanillaX\session\Session;
use pocketmine\block\Block;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\math\RayTraceResult;

class ShulkerBulletEntity extends Projectile{

    public $width = 0.625;
    public $height = 0.625;

    protected $gravity = 0.05;

    const NETWORK_ID = self::SHULKER_BULLET;

    protected $damage = 4;

    protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult): void{
        if($entityHit instanceof Living){
            $entityHit->addEffect(new EffectInstance(Effect::getEffect(Effect::LEVITATION), 200, 1));
        }
        parent::onHitEntity($entityHit, $hitResult);
    }

    protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult): void{
        parent::onHitBlock($blockHit, $hitResult);
        $this->getLevel()->addParticle(new ExplodeParticle($blockHit));
        $pk = Session::playSound($this, "bullet.hit");
        $this->getLevel()->broadcastPacketToViewers($this, $pk);
        $this->flagForDespawn();
    }
}