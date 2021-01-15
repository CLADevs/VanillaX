<?php

namespace CLADevs\VanillaX\blocks\types;

use CLADevs\VanillaX\blocks\TileIdentifiers;
use CLADevs\VanillaX\blocks\tiles\HopperTile;
use pocketmine\block\Block;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Tile;

class HopperBlock extends Transparent{

    public function __construct(int $meta = 0){
        parent::__construct(self::HOPPER_BLOCK, $meta, "Hopper");
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        $this->meta = $faces[$face] ?? $face;
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        Tile::createTile(TileIdentifiers::HOPPER, $this->getLevel(), HopperTile::createNBT($this));
        return true;
    }

    public function onActivate(Item $item, Player $player = null): bool{
        if($player !== null){
            /** @var HopperTile|null $tile */
            $tile = $this->getLevel()->getTile($this);

            if($tile === null){
                $tile = Tile::createTile(TileIdentifiers::HOPPER, $this->getLevel(), HopperTile::createNBT($this));;
            }
            if($tile instanceof HopperTile){
                $player->addWindow($tile->getInventory());
            }
        }
        return true;
    }

    public function hasEntityCollision(): bool{
        return true;
    }

//    public function onEntityCollide(Entity $entity): void{
//        if($entity instanceof ItemEntity){
//            var_dump('Collided');
//            /** @var HopperTile|null $tile */
//            $tile = $this->getLevel()->getTile($this);
//
//            if($tile === null){
//                $tile = Tile::createTile(TileIdentifiers::HOPPER, $this->getLevel(), HopperTile::createNBT($this));;
//            }
//            $tile->onItemCollide($entity);
//        }
//    }
}