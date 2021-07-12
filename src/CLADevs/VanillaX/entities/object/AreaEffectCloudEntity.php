<?php

namespace CLADevs\VanillaX\entities\object;

use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\entity\effect\Effect;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class AreaEffectCloudEntity extends Entity{

    const NETWORK_ID = EntityIds::AREA_EFFECT_CLOUD;

    public float $width = 3;
    public float $height = 1;

    private float $radius = 3.0;
    private float $radiusPerTick = -0.05;
    private float $radiusChangeOnPickup = -0.5;

    private int $duration = 600;
    private int $waiting = 20;
    private int $spawnTime = 0;
    private int $pickupCount = 0;

    private ?Effect $effect;
    
    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $effect = EffectIdMap::getInstance()->fromId($effectId = $nbt->getShort("PotionId", 0));
        $this->effect = $effect;
        if(($tag = $nbt->getTag(EntityMetadataProperties::AREA_EFFECT_CLOUD_RADIUS)) !== null){
            $this->radius = $tag->getValue();
        }
        if(($tag = $nbt->getTag(EntityMetadataProperties::AREA_EFFECT_CLOUD_RADIUS)) !== null){
            $this->radius = $nbt->getFloat(EntityMetadataProperties::AREA_EFFECT_CLOUD_RADIUS, $this->radius);
        }
        if(($tag = $nbt->getTag(EntityMetadataProperties::AREA_EFFECT_CLOUD_RADIUS_PER_TICK)) !== null){
            $this->radiusPerTick = $nbt->getFloat(EntityMetadataProperties::AREA_EFFECT_CLOUD_RADIUS_PER_TICK, $this->radiusPerTick);
        }
        if(($tag = $nbt->getTag(EntityMetadataProperties::AREA_EFFECT_CLOUD_RADIUS_CHANGE_ON_PICKUP)) !== null){
            $this->radiusChangeOnPickup = $nbt->getFloat(EntityMetadataProperties::AREA_EFFECT_CLOUD_RADIUS_CHANGE_ON_PICKUP, $this->radiusChangeOnPickup);
        }
        if(($tag = $nbt->getTag(EntityMetadataProperties::AREA_EFFECT_CLOUD_DURATION)) !== null){
            $this->duration = $nbt->getInt(EntityMetadataProperties::AREA_EFFECT_CLOUD_DURATION, $this->duration);
        }
        if(($tag = $nbt->getTag(EntityMetadataProperties::AREA_EFFECT_CLOUD_WAITING)) !== null){
            $this->waiting = $nbt->getInt(EntityMetadataProperties::AREA_EFFECT_CLOUD_WAITING, $this->waiting);
        }
        if(($tag = $nbt->getTag(EntityMetadataProperties::AREA_EFFECT_CLOUD_SPAWN_TIME)) !== null){
            $this->spawnTime = $nbt->getInt(EntityMetadataProperties::AREA_EFFECT_CLOUD_SPAWN_TIME, $this->spawnTime);
        }
        if(($tag = $nbt->getTag(EntityMetadataProperties::AREA_EFFECT_CLOUD_PICKUP_COUNT)) !== null){
            $this->pickupCount = $nbt->getInt(EntityMetadataProperties::AREA_EFFECT_CLOUD_PICKUP_COUNT, $this->pickupCount);
        }
        $this->getNetworkProperties()->setInt(EntityMetadataProperties::AREA_EFFECT_CLOUD_PARTICLE_ID, $effectId);
        $this->getNetworkProperties()->setFloat(EntityMetadataProperties::AREA_EFFECT_CLOUD_RADIUS, $this->radius);
        $this->getNetworkProperties()->setFloat(EntityMetadataProperties::AREA_EFFECT_CLOUD_RADIUS_PER_TICK, $this->radiusPerTick);
        $this->getNetworkProperties()->setFloat(EntityMetadataProperties::AREA_EFFECT_CLOUD_RADIUS_CHANGE_ON_PICKUP, $this->radiusChangeOnPickup);
        $this->getNetworkProperties()->setInt(EntityMetadataProperties::AREA_EFFECT_CLOUD_DURATION, $this->duration);
        $this->getNetworkProperties()->setInt(EntityMetadataProperties::AREA_EFFECT_CLOUD_WAITING, $this->waiting);
        $this->getNetworkProperties()->setInt(EntityMetadataProperties::AREA_EFFECT_CLOUD_SPAWN_TIME, $this->spawnTime);
        $this->getNetworkProperties()->setInt(EntityMetadataProperties::AREA_EFFECT_CLOUD_PICKUP_COUNT, $this->pickupCount);
    }

    public function entityBaseTick(int $tickDiff = 1): bool{
        $parent = parent::entityBaseTick($tickDiff);
        if($this->duration < 1){
            $this->flagForDespawn();
            return false;
        }
        $this->duration -= $this->radiusPerTick;
        $this->getNetworkProperties()->setInt(EntityMetadataProperties::AREA_EFFECT_CLOUD_DURATION, $this->duration);
        return $parent;
    }
    
    public function saveNBT(): CompoundTag{
        $nbt = parent::saveNBT();
        $nbt->setShort("PotionId", EffectIdMap::getInstance()->toId($this->effect));
        $nbt->setFloat(EntityMetadataProperties::AREA_EFFECT_CLOUD_RADIUS, $this->radius);
        $nbt->setFloat(EntityMetadataProperties::AREA_EFFECT_CLOUD_RADIUS_PER_TICK, $this->radiusPerTick);
        $nbt->setFloat(EntityMetadataProperties::AREA_EFFECT_CLOUD_RADIUS_CHANGE_ON_PICKUP, $this->radiusChangeOnPickup);
        $nbt->setInt(EntityMetadataProperties::AREA_EFFECT_CLOUD_DURATION, $this->duration);
        $nbt->setInt(EntityMetadataProperties::AREA_EFFECT_CLOUD_WAITING, $this->waiting);
        $nbt->setInt(EntityMetadataProperties::AREA_EFFECT_CLOUD_SPAWN_TIME, $this->spawnTime);
        $nbt->setInt(EntityMetadataProperties::AREA_EFFECT_CLOUD_PICKUP_COUNT, $this->pickupCount);
        return $nbt;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }
}