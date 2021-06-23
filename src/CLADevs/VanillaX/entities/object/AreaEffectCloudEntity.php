<?php

namespace CLADevs\VanillaX\entities\object;

use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;

class AreaEffectCloudEntity extends Entity{

    const NETWORK_ID = self::AREA_EFFECT_CLOUD;

    public $width = 3;
    public $height = 1;

    private float $radius = 3.0;
    private float $radiusPerTick = -0.05;
    private float $radiusChangeOnPickup = -0.5;

    private int $duration = 600;
    private int $waiting = 20;
    private int $spawnTime = 0;
    private int $pickupCount = 0;

    private ?Effect $effect;

    public function __construct(Level $level, CompoundTag $nbt){
        parent::__construct($level, $nbt);
        $effect = Effect::getEffect($effectId = $nbt->getShort("PotionId", 0));
        $this->effect = $effect;
        if($nbt->hasTag(self::DATA_AREA_EFFECT_CLOUD_RADIUS, FloatTag::class)){
            $this->radius = $nbt->getFloat(self::DATA_AREA_EFFECT_CLOUD_RADIUS, $this->radius);
        }
        if($nbt->hasTag(self::DATA_AREA_EFFECT_CLOUD_RADIUS_PER_TICK, FloatTag::class)){
            $this->radiusPerTick = $nbt->getFloat(self::DATA_AREA_EFFECT_CLOUD_RADIUS_PER_TICK, $this->radiusPerTick);
        }
        if($nbt->hasTag(self::DATA_AREA_EFFECT_CLOUD_RADIUS_CHANGE_ON_PICKUP, FloatTag::class)){
            $this->radiusChangeOnPickup = $nbt->getFloat(self::DATA_AREA_EFFECT_CLOUD_RADIUS_CHANGE_ON_PICKUP, $this->radiusChangeOnPickup);
        }
        if($nbt->hasTag(self::DATA_AREA_EFFECT_CLOUD_DURATION, IntTag::class)){
            $this->duration = $nbt->getInt(self::DATA_AREA_EFFECT_CLOUD_DURATION, $this->duration);
        }
        if($nbt->hasTag(self::DATA_AREA_EFFECT_CLOUD_WAITING, IntTag::class)){
            $this->waiting = $nbt->getInt(self::DATA_AREA_EFFECT_CLOUD_WAITING, $this->waiting);
        }
        if($nbt->hasTag(self::DATA_AREA_EFFECT_CLOUD_SPAWN_TIME, IntTag::class)){
            $this->spawnTime = $nbt->getInt(self::DATA_AREA_EFFECT_CLOUD_SPAWN_TIME, $this->spawnTime);
        }
        if($nbt->hasTag(self::DATA_AREA_EFFECT_CLOUD_PICKUP_COUNT, IntTag::class)){
            $this->pickupCount = $nbt->getInt(self::DATA_AREA_EFFECT_CLOUD_PICKUP_COUNT, $this->pickupCount);
        }
        $this->propertyManager->setInt(self::DATA_AREA_EFFECT_CLOUD_PARTICLE_ID, $effectId);
        $this->propertyManager->setFloat(self::DATA_AREA_EFFECT_CLOUD_RADIUS, $this->radius);
        $this->propertyManager->setFloat(self::DATA_AREA_EFFECT_CLOUD_RADIUS_PER_TICK, $this->radiusPerTick);
        $this->propertyManager->setFloat(self::DATA_AREA_EFFECT_CLOUD_RADIUS_CHANGE_ON_PICKUP, $this->radiusChangeOnPickup);
        $this->propertyManager->setInt(self::DATA_AREA_EFFECT_CLOUD_DURATION, $this->duration);
        $this->propertyManager->setInt(self::DATA_AREA_EFFECT_CLOUD_WAITING, $this->waiting);
        $this->propertyManager->setInt(self::DATA_AREA_EFFECT_CLOUD_SPAWN_TIME, $this->spawnTime);
        $this->propertyManager->setInt(self::DATA_AREA_EFFECT_CLOUD_PICKUP_COUNT, $this->pickupCount);
    }

    public function entityBaseTick(int $tickDiff = 1): bool{
        $parent = parent::entityBaseTick($tickDiff);
        if($this->duration < 1){
            $this->flagForDespawn();
            return false;
        }
        $this->duration -= $this->radiusPerTick;
        $this->propertyManager->setInt(self::DATA_AREA_EFFECT_CLOUD_DURATION, $this->duration);
        return $parent;
    }
    
    public function saveNBT(): void{
        parent::saveNBT();
        $this->namedtag->setShort("PotionId", $this->effect->getId());
        $this->namedtag->setFloat(self::DATA_AREA_EFFECT_CLOUD_RADIUS, $this->radius);
        $this->namedtag->setFloat(self::DATA_AREA_EFFECT_CLOUD_RADIUS_PER_TICK, $this->radiusPerTick);
        $this->namedtag->setFloat(self::DATA_AREA_EFFECT_CLOUD_RADIUS_CHANGE_ON_PICKUP, $this->radiusChangeOnPickup);
        $this->namedtag->setInt(self::DATA_AREA_EFFECT_CLOUD_DURATION, $this->duration);
        $this->namedtag->setInt(self::DATA_AREA_EFFECT_CLOUD_WAITING, $this->waiting);
        $this->namedtag->setInt(self::DATA_AREA_EFFECT_CLOUD_SPAWN_TIME, $this->spawnTime);
        $this->namedtag->setInt(self::DATA_AREA_EFFECT_CLOUD_PICKUP_COUNT, $this->pickupCount);
    }
}