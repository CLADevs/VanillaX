<?php

namespace CLADevs\VanillaX\entities\projectile;

use CLADevs\VanillaX\entities\utils\EntityCustomRegisterClosure;
use CLADevs\VanillaX\entities\utils\EntityCustomSaveNames;
use Closure;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper as Helper;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntityCombustByEntityEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector3;
use pocketmine\math\VoxelRayTrace;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\timings\Timings;
use pocketmine\world\World;

class ArrowEntity extends Arrow implements EntityCustomRegisterClosure, EntityCustomSaveNames{

    private int $pierce = 0;

    /** @var int[] */
    private array $piercedEntities = [];

    protected function move(float $dx, float $dy, float $dz) : void{
        $this->blocksAround = null;

        Timings::$entityMove->startTiming();

        $start = $this->location->asVector3();
        $end = $start->add($dx, $dy, $dz);

        $blockHit = null;
        $entityHit = null;
        $hitResult = null;

        foreach(VoxelRayTrace::betweenPoints($start, $end) as $vector3){
            $block = $this->getWorld()->getBlockAt($vector3->x, $vector3->y, $vector3->z);

            $blockHitResult = $this->calculateInterceptWithBlock($block, $start, $end);
            if($blockHitResult !== null){
                $end = $blockHitResult->hitVector;
                $blockHit = $block;
                $hitResult = $blockHitResult;
                break;
            }
        }

        $entityDistance = PHP_INT_MAX;

        $newDiff = $end->subtractVector($start);
        foreach($this->getWorld()->getCollidingEntities($this->boundingBox->addCoord($newDiff->x, $newDiff->y, $newDiff->z)->expand(1, 1, 1), $this) as $entity){
            if($entity->getId() === $this->getOwningEntityId() && $this->ticksLived < 5){
                continue;
            }

            $entityBB = $entity->boundingBox->expandedCopy(0.3, 0.3, 0.3);
            $entityHitResult = $entityBB->calculateIntercept($start, $end);

            if($entityHitResult === null){
                continue;
            }

            $distance = $this->location->distanceSquared($entityHitResult->hitVector);

            if($distance < $entityDistance){
                if(in_array($entity->getId(), $this->piercedEntities)){
                    continue;
                }
                $entityDistance = $distance;
                $entityHit = $entity;
                $hitResult = $entityHitResult;
                $end = $entityHitResult->hitVector;
            }
        }

        $this->location = Location::fromObject(
            $end,
            $this->location->world,
            $this->location->yaw,
            $this->location->pitch
        );
        $this->recalculateBoundingBox();

        if($hitResult !== null){
            /** @var ProjectileHitEvent|null $ev */
            $ev = null;
            if($entityHit !== null){
                $ev = new ProjectileHitEntityEvent($this, $hitResult, $entityHit);
            }elseif($blockHit !== null){
                $ev = new ProjectileHitBlockEvent($this, $hitResult, $blockHit);
            }else{
                assert(false, "unknown hit type");
            }

            if($ev !== null){
                $ev->call();
                $this->onHit($ev);

                if($ev instanceof ProjectileHitEntityEvent){
                    $this->onHitEntity($ev->getEntityHit(), $ev->getRayTraceResult());
                }elseif($ev instanceof ProjectileHitBlockEvent){
                    $this->onHitBlock($ev->getBlockHit(), $ev->getRayTraceResult());
                }
            }

            if($this->isAllEntityPierced() || $ev instanceof ProjectileHitBlockEvent){
                $this->isCollided = $this->onGround = true;
                $this->motion = new Vector3(0, 0, 0);
            }
        }else{
            $this->isCollided = $this->onGround = false;
            $this->blockHit = null;

            //recompute angles...
            $f = sqrt(($this->motion->x ** 2) + ($this->motion->z ** 2));
            $this->setRotation(
                atan2($this->motion->x, $this->motion->z) * 180 / M_PI,
                atan2($this->motion->y, $f) * 180 / M_PI
            );
        }

        $this->getWorld()->onEntityMoved($this);
        $this->checkBlockIntersections();

        Timings::$entityMove->stopTiming();
    }

    protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult): void{
        $this->piercedEntities[] = $entityHit->getId();
        $damage = $this->getResultDamage();

        if($damage >= 0){
            if($this->getOwningEntity() === null){
                $ev = new EntityDamageByEntityEvent($this, $entityHit, EntityDamageEvent::CAUSE_PROJECTILE, $damage);
            }else{
                $ev = new EntityDamageByChildEntityEvent($this->getOwningEntity(), $this, $entityHit, EntityDamageEvent::CAUSE_PROJECTILE, $damage);
            }

            $entityHit->attack($ev);

            if($this->isOnFire()){
                $ev = new EntityCombustByEntityEvent($this, $entityHit, 5);
                $ev->call();
                if(!$ev->isCancelled()){
                    $entityHit->setOnFire($ev->getDuration());
                }
            }
        }
        if($this->pierce < 1){
            $this->flagForDespawn();
            return;
        }
        if($this->isAllEntityPierced()) $this->flagForDespawn();
    }

    public function isAllEntityPierced(): bool{
        return count($this->piercedEntities) >= ($this->pierce + 1);
    }

    public function getPierce(): int{
        return $this->pierce;
    }

    public function setPierce(int $pierce): void{
        $this->pierce = $pierce;
    }

    public static function getRegisterClosure(): Closure{
        return function(World $world, CompoundTag $nbt): ArrowEntity{
            return new ArrowEntity(Helper::parseLocation($nbt, $world), null, $nbt->getByte(self::TAG_CRIT, 0) === 1, $nbt);
        };
    }

    public static function getSaveNames(): array{
        return ['Arrow', 'minecraft:arrow'];
    }

    public static function canRegister(): bool{
        return true;
    }
}