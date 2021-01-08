<?php

namespace CLADevs\VanillaX\blocks\types;

use CLADevs\VanillaX\blocks\tiles\MobSpawnerTile;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\block\MonsterSpawner;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Tile;

class MobSpawnerBlock extends MonsterSpawner{

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        $nbt = MobSpawnerTile::createNBT($this);
        $nbt->setInt("entityId", $item->getDamage());
        Tile::createTile(Tile::MOB_SPAWNER, $this->getLevel(),$nbt);
        return true;
    }

    public function onActivate(Item $item, Player $player = null): bool{
        if($item->getId() === ItemIds::SPAWN_EGG){
            $tile = $this->getLevel()->getTile($this);

            if(!$tile instanceof MobSpawnerTile){
                $tile = Tile::createTile(Tile::MOB_SPAWNER, $this->getLevel(), MobSpawnerTile::createNBT($this));
            }
            if($tile->getEntityId() !== ($newId = $item->getDamage())){
                $tile->setEntityId($newId);
                if(!$player->isCreative()) $item->pop();
                return true;
            }
        }
        return false;
    }

    public function isAffectedBySilkTouch(): bool{
        return true;
    }

    public function getSilkTouchDrops(Item $item): array{
        $drop = [];
        $tile = $this->getLevel()->getTile($this);

        if($tile instanceof MobSpawnerTile){
            $drop[] = ItemFactory::get(BlockIds::MOB_SPAWNER, ($tile->isValidEntity() ? $tile->getEntityId() : 0));
        }
        return $drop;
    }
}