<?php

namespace CLADevs\VanillaX\entities\projectile;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\projectile\Throwable;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class LingeringPotionEntity extends Throwable{

    const NETWORK_ID = EntityIds::LINGERING_POTION;

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.1, 0.1);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }

//    protected float $gravity = 0.05;
//
//    protected function initEntity(CompoundTag $nbt): void{
//        parent::initEntity($nbt);
//        $this->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::LINGER, true);
//        $this->getNetworkProperties()->setShort(EntityMetadataProperties::POTION_AUX_VALUE, $nbt->getShort("PotionId", 0));
//    }
//
//    public function saveNBT(): CompoundTag{
//        $nbt = parent::saveNBT();
//        $nbt->setShort("PotionId", $this->getPotionId());
//        return $nbt;
//    }
//
//    protected function onHit(ProjectileHitEvent $event) : void{
//        $effects = $this->getPotionEffects();
//        $hasEffects = true;
//
//        if(count($effects) === 0){
//            $colors = [
//                new Color(0x38, 0x5d, 0xc6) //Default colour for splash water bottle and similar with no effects.
//            ];
//            $hasEffects = false;
//        }else{
//            $colors = [];
//            foreach($effects as $effect){
//                $level = $effect->getEffectLevel();
//                for($j = 0; $j < $level; ++$j){
//                    $colors[] = $effect->getColor();
//                }
//            }
//        }
//
//        $this->getWorld()->addParticle($this->getPosition(), new PotionSplashParticle(Color::mix(...$colors)));
//        $this->getWorld()->addSound($this->getPosition(), new PotionSplashSound());
//
//        if($hasEffects){
//            $effectId = -1;
//            foreach($effects as $effect){
//                $effectId = $effect->getId();
//            }
//            if($effectId !== -1){
//                $nbt = AreaEffectCloudEntity::createBaseNBT($this);
//                $nbt->setShort("PotionId", $effectId);
//                $areaCloudEffect = new AreaEffectCloudEntity($this->getLevel(), $nbt);
//                $areaCloudEffect->spawnToAll();
//            }
//        }elseif($event instanceof ProjectileHitBlockEvent and $this->getPotionId() === Potion::WATER){
//            $blockIn = $event->getBlockHit()->getSide($event->getRayTraceResult()->getHitFace());
//
//            if($blockIn->getId() === Block::FIRE){
//                $this->level->setBlock($blockIn, BlockFactory::get(Block::AIR));
//            }
//            foreach($blockIn->getHorizontalSides() as $horizontalSide){
//                if($horizontalSide->getId() === Block::FIRE){
//                    $this->level->setBlock($horizontalSide, BlockFactory::get(Block::AIR));
//                }
//            }
//        }
//    }
//
//    public function getResultDamage(): int{
//        return -1; //no damage
//    }
//
//    /**
//     * @return EffectInstance[]
//     */
//    public function getPotionEffects(): array{
//        return Potion::getPotionEffectsById($this->getPotionId());
//    }
//
//    public function getPotionId(): int{
//        return $this->propertyManager->getShort(self::DATA_POTION_AUX_VALUE) ?? 0;
//    }
}