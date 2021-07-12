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
}