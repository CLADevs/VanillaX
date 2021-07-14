<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\BlockManager;
use CLADevs\VanillaX\blocks\utils\TileVanilla;
use CLADevs\VanillaX\entities\EntityManager;
use CLADevs\VanillaX\entities\utils\EntityIdentifierX;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Spawnable;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;

class MobSpawnerTile extends Spawnable{

    const TILE_ID = TileVanilla::MOB_SPAWNER;
    const TILE_BLOCK = BlockLegacyIds::MOB_SPAWNER;

    const TAG_ENTITY_ID = "EntityId";
    const TAG_ENTITY_IDENTIFIER = "EntityIdentifier";
    const TAG_DISPLAY_ENTITY_SCALE = "DisplayEntityScale";
    const TAG_SPAWN_COUNT = "SpawnCount";
    const TAG_SPAWN_RANGE = "SpawnRange";
    const TAG_MIN_SPAWN_DELAY = "MinSpawnDelay";
    const TAG_MAX_SPAWN_DELAY = "MaxSpawnDelay";

    private bool $validEntity = true;

    private int $entityId = -1;
    private int $spawnCount = 4;
    private int $spawnRange = 4;
    private int $spawnDelay = 20;
    private int $minSpawnDelay = 200;
    private int $maxSpawnDelay = 800;
    private int $tick = 20;

    private ?EntityIdentifierX $entity = null;

    public function getEntityId(): int{
        return $this->entityId;
    }

    public function setEntityId(int $entityId): void{
        $previousEntity = $this->entity;
        foreach(EntityManager::getInstance()->getEntities() as $entity){
            if($entity->getId() === $entityId){
                $this->entity = $entity;
                break;
            }
        }
        $this->entityId = $entityId;
        $this->validEntity = true;
        $this->setDirty();
        BlockManager::onChange($this);
        if($previousEntity === null && $this->entity !== null){
            $this->pos->getWorld()->scheduleDelayedBlockUpdate($this->pos, 1);
        }
    }

    public function getEntity(): ?EntityIdentifierX{
        return $this->entity;
    }

    public function getSpawnCount(): int{
        return $this->spawnCount;
    }

    public function setSpawnCount(int $spawnCount): void{
        $this->spawnCount = $spawnCount;
    }

    public function getSpawnRange(): int{
        return $this->spawnRange;
    }

    public function setSpawnRange(int $spawnRange): void{
        $this->spawnRange = $spawnRange;
    }

    public function getSpawnDelay(): int{
        return $this->spawnDelay;
    }

    public function setSpawnDelay(int $spawnDelay): void{
        $this->spawnDelay = $spawnDelay;
    }

    public function decreaseSpawnDelay(): void{
        $this->spawnDelay--;
    }

    public function getMinSpawnDelay(): int{
        return $this->minSpawnDelay;
    }

    public function setMinSpawnDelay(int $minSpawnDelay): void{
        $this->minSpawnDelay = $minSpawnDelay;
    }

    public function getMaxSpawnDelay(): int{
        return $this->maxSpawnDelay;
    }

    public function setMaxSpawnDelay(int $maxSpawnDelay): void{
        $this->maxSpawnDelay = $maxSpawnDelay;
    }

    public function getTick(): int{
        return $this->tick;
    }

    public function setTick(int $tick): void{
        $this->tick = $tick;
    }

    public function decreaseTick(): void{
        $this->tick--;
    }

    public function isValidEntity(): bool{
        return $this->validEntity;
    }

    public function canEntityGenerate(): bool{
        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            if($player->getPosition()->distance($this->getPos()) < 16){
                return true;
            }
        }
        return false;
    }

    public function readSaveData(CompoundTag $nbt): void{
        if(($tag = $nbt->getTag(self::TAG_ENTITY_ID)) !== null){
            $this->setEntityId($nbt->getInt(self::TAG_ENTITY_ID));
        }
        if(($tag = $nbt->getTag(self::TAG_SPAWN_COUNT)) !== null){
            $this->spawnCount = $nbt->getInt(self::TAG_SPAWN_COUNT);
        }
        if(($tag = $nbt->getTag(self::TAG_SPAWN_RANGE)) !== null){
            $this->spawnRange = $nbt->getInt(self::TAG_SPAWN_RANGE);
        }
        if(($tag = $nbt->getTag(self::TAG_MIN_SPAWN_DELAY)) !== null){
            $this->minSpawnDelay = $nbt->getInt(self::TAG_MIN_SPAWN_DELAY);
        }
        if(($tag = $nbt->getTag(self::TAG_MAX_SPAWN_DELAY)) !== null){
            $this->maxSpawnDelay = $nbt->getInt(self::TAG_MAX_SPAWN_DELAY);
        }
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $nbt->setInt(self::TAG_ENTITY_ID, $this->entityId);
        $nbt->setInt(self::TAG_SPAWN_COUNT, $this->spawnCount);
        $nbt->setInt(self::TAG_SPAWN_RANGE, $this->spawnRange);
        $nbt->setInt(self::TAG_MIN_SPAWN_DELAY, $this->minSpawnDelay);
        $nbt->setInt(self::TAG_MAX_SPAWN_DELAY, $this->maxSpawnDelay);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
        if($this->entity !== null){
            $nbt->setString(self::TAG_ENTITY_IDENTIFIER, $this->entity->getMcpeId());
            $nbt->setFloat(self::TAG_DISPLAY_ENTITY_SCALE, 1.0);
        }
    }
}