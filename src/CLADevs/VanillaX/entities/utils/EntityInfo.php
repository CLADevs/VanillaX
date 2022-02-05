<?php

namespace CLADevs\VanillaX\entities\utils;

use CLADevs\VanillaX\entities\VanillaEntity;

class EntityInfo{

    const TYPE_NONE = "none";
    const TYPE_BOSS = "boss";
    const TYPE_MONSTER = "monster";
    const TYPE_NEUTRAL = "neutral";
    const TYPE_PASSIVE = "passive";
    const TYPE_PROJECTILE = "projectile";
    const TYPE_OBJECT = "object";

    private string $type;
    private string $name;
    private string $displayName;
    private string $class;

    private int $legacyId;

    public function __construct(string $type, string $name, string $displayName, string $class, int $legacyId){
        $this->type = $type;
        $this->name = $name;
        $this->displayName = $legacyId === VanillaEntity::VILLAGER_V2 ? "Villager" : $displayName;
        $this->class = $class;
        $this->legacyId = $legacyId;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getDisplayName(): string{
        return $this->displayName;
    }

    public function getLegacyId(): int{
        return $this->legacyId;
    }

    public function getClass(): string{
        return $this->class;
    }

    public function isNone(): bool{
        return $this->type === self::TYPE_NONE;
    }

    public function isBoss(): bool{
        return $this->type === self::TYPE_BOSS;
    }

    public function isMonster(): bool{
        return $this->type === self::TYPE_MONSTER;
    }

    public function isNeutral(): bool{
        return $this->type === self::TYPE_NEUTRAL;
    }

    public function isPassive(): bool{
        return $this->type === self::TYPE_PASSIVE;
    }

    public function isProjectile(): bool{
        return $this->type === self::TYPE_PROJECTILE;
    }

    public function isObject(): bool{
        return $this->type === self::TYPE_OBJECT;
    }
}