<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\MobSpawnerTile;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\MonsterSpawner;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\ToolTier;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class MobSpawnerBlock extends MonsterSpawner{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::MOB_SPAWNER, 0, null, MobSpawnerTile::class), "Monster Spawner", new BlockBreakInfo(5.0, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel()));
    }

    public function isAffectedBySilkTouch(): bool{
        return true;
    }

    public function getSilkTouchDrops(Item $item): array{
        $drop = [];
        $tile = $this->getPos()->getWorld()->getTile($this->getPos());

        if($tile instanceof MobSpawnerTile){
            $drop[] = ItemFactory::getInstance()->get(BlockLegacyIds::MOB_SPAWNER, ($tile->isValidEntity() ? $tile->getEntityId() : 0));
        }
        return $drop;
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        if($item->getId() === ItemIds::SPAWN_EGG){
            $tile = $this->getPos()->getWorld()->getTile($this->getPos());

            if($tile instanceof MobSpawnerTile && $tile->getEntityId() !== ($newId = $item->getMeta())){
                $tile->setEntityId($newId);
                if(!$player->isCreative()) $item->pop();
                return true;
            }
        }
        return false;
    }
    
    public function onScheduledUpdate(): void{
        $tile = $this->pos->getWorld()->getTile($this->pos);

        if($tile->isClosed() || !$tile instanceof MobSpawnerTile){
            return;
        }
        if($tile->getTick() > 0) $tile->decreaseTick();
        if($tile->isValidEntity() && $tile->canEntityGenerate() && $tile->getTick() <= 0){
            $tile->setTick(20);
            if($tile->getSpawnDelay() > 0){
                $tile->decreaseSpawnDelay();
            }else{
                $tile->setSpawnDelay($tile->getMinSpawnDelay() + mt_rand(0, min(0, $tile->getMaxSpawnDelay() - $tile->getMinSpawnDelay())));

                for($i = 0; $i < $tile->getSpawnCount(); $i++){
                    $x = ((mt_rand(-10, 10) / 10) * $tile->getSpawnRange()) + 0.5;
                    $z = ((mt_rand(-10, 10) / 10) * $tile->getSpawnRange()) + 0.5;
                    //TODO spawn an entity
                    //  $entity = Entity::createEntity($tile->getEntityId(), $tile->getLevel(), Entity::createBaseNBT($tile->add($x, mt_rand(1, 3), $z)));

//                    if($entity === null){
//                        $tile->validEntity = false;
//                        return true;
//                    }
                    // $entity->spawnToAll();
                }
            }
        }
        $this->pos->getWorld()->scheduleDelayedBlockUpdate($this->pos, 1);
    }
}