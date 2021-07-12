<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\MobSpawnerTile;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\MonsterSpawner;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class MobSpawnerBlock extends MonsterSpawner{

    //TODO tile

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
}