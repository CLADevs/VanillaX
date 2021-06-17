<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\MobSpawnerTile;
use CLADevs\VanillaX\blocks\utils\TileUtils;
use CLADevs\VanillaX\blocks\utils\TileVanilla;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\block\MonsterSpawner;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;

class MobSpawnerBlock extends MonsterSpawner{

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
    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        TileUtils::generateTile($this, TileVanilla::MOB_SPAWNER, MobSpawnerTile::class, [new IntTag(MobSpawnerTile::TAG_ENTITY_ID, $item->getDamage())]);
        return true;
    }

    public function onActivate(Item $item, Player $player = null): bool{
        if($item->getId() === ItemIds::SPAWN_EGG){
            $tile = $this->getLevel()->getTile($this);

            if($tile instanceof MobSpawnerTile && $tile->getEntityId() !== ($newId = $item->getDamage())){
                $tile->setEntityId($newId);
                if(!$player->isCreative()) $item->pop();
                return true;
            }
        }
        return false;
    }
}