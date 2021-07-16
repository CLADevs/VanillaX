<?php

namespace CLADevs\VanillaX\entities\utils;

use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\data\bedrock\EntityLegacyIds;

class EntityIdentifierX{

    const TYPE_NONE = "none";
    const TYPE_BOSS = "boss";
    const TYPE_MONSTER = "monster";
    const TYPE_NEUTRAL = "neutral";
    const TYPE_OBJECT = "object";
    const TYPE_PASSIVE = "passive";
    const TYPE_PROJECTILE = "projectile";

    private string $mcpeId;
    private string $name;
    private string $namespace;
    private string $type;

    private int $id;

    public function __construct(string $mcpeId, string $entityName, string $namespace, string $type, int $entityId){
        if($entityId === VanillaEntity::VILLAGER_V2){
            $entityId = EntityLegacyIds::VILLAGER;
        }
        $this->mcpeId = $mcpeId;
        $this->name = $entityName;
        $this->namespace = $namespace;
        $this->type = $type;
        $this->id = $entityId;
    }

    public function getMcpeId(): string{
        return $this->mcpeId;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getNamespace(): string{
        return $this->namespace;
    }

    public function getType(): string{
        return $this->type;
    }

    public function getId(): int{
        return $this->id;
    }
}