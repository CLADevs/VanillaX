<?php

namespace CLADevs\VanillaX\entities\projectile;

use CLADevs\VanillaX\session\Session;
use pocketmine\block\Block;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\entity\projectile\Projectile;
use pocketmine\math\RayTraceResult;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\world\particle\ExplodeParticle;

class ShulkerBulletEntity extends Projectile{

    public float $width = 0.625;
    public float $height = 0.625;

    /** @var float */
    protected $gravity = 0.05;

    const NETWORK_ID = EntityIds::SHULKER_BULLET;

    protected $damage = 4;

    protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult): void{
        if($entityHit instanceof Living){
            $entityHit->getEffects()->add(new EffectInstance(VanillaEffects::LEVITATION(), 200, 1));
        }
        parent::onHitEntity($entityHit, $hitResult);
    }

    protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult): void{
        parent::onHitBlock($blockHit, $hitResult);
        $this->getWorld()->addParticle($blockHit->getPos(), new ExplodeParticle());
        $pk = Session::playSound($this->getPosition(), "bullet.hit");
        $this->getWorld()->broadcastPacketToViewers($this->getPosition(), $pk);
        $this->flagForDespawn();
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
}