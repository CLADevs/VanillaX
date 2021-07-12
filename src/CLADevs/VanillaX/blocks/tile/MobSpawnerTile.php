<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\utils\TileVanilla;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Spawnable;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;

class MobSpawnerTile extends Spawnable{

    const TILE_ID = TileVanilla::MOB_SPAWNER;
    const TILE_BLOCK = BlockLegacyIds::MOB_SPAWNER;

    const TAG_ENTITY_ID = "EntityId";
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

    public function getEntityId(): int{
        return $this->entityId;
    }

    public function setEntityId(int $entityId): void{
        $this->entityId = $entityId;
        $this->validEntity = true;
     //   $this->onChanged();
        //TODO onChange
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

    public function onUpdate(): bool{
        if($this->tick > 0) $this->tick--;
        if($this->validEntity && $this->canEntityGenerate() && $this->tick <= 0){
            $this->tick = 20;
            if($this->spawnDelay > 0){
                $this->spawnDelay--;
            }else{
                $this->spawnDelay = $this->minSpawnDelay + mt_rand(0, min(0, $this->maxSpawnDelay - $this->minSpawnDelay));

                for($i = 0; $i < $this->spawnCount; $i++){
                    $x = ((mt_rand(-10, 10) / 10) * $this->spawnRange) + 0.5;
                    $z = ((mt_rand(-10, 10) / 10) * $this->spawnRange) + 0.5;
                    //TODO spawn an entity
                  //  $entity = Entity::createEntity($this->getEntityId(), $this->getLevel(), Entity::createBaseNBT($this->add($x, mt_rand(1, 3), $z)));

//                    if($entity === null){
//                        $this->validEntity = false;
//                        return true;
//                    }
                   // $entity->spawnToAll();
                }
            }
        }
        return true;
    }

    public function readSaveData(CompoundTag $nbt): void{
        if(($tag = $nbt->getTag(self::TAG_ENTITY_ID)) !== null){
            $this->entityId = $nbt->getInt(self::TAG_ENTITY_ID);
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
       // $this->scheduleUpdate();
        //TODO schedule
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $nbt->setInt(self::TAG_ENTITY_ID, $this->entityId);
        $nbt->setInt(self::TAG_SPAWN_COUNT, $this->spawnCount);
        $nbt->setInt(self::TAG_SPAWN_RANGE, $this->spawnRange);
        $nbt->setInt(self::TAG_MIN_SPAWN_DELAY, $this->minSpawnDelay);
        $nbt->setInt(self::TAG_MAX_SPAWN_DELAY, $this->maxSpawnDelay);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
        $nbt->setInt(self::TAG_ENTITY_ID, $this->entityId);
    }
}