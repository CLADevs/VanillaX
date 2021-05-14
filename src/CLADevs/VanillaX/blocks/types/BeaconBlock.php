<?php

namespace CLADevs\VanillaX\blocks\types;

use CLADevs\VanillaX\blocks\TileIdentifiers;
use CLADevs\VanillaX\blocks\tiles\BeaconTile;
use CLADevs\VanillaX\inventories\types\BeaconInventory;
use pocketmine\block\Block;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Tile;

class BeaconBlock extends Transparent{

    public function __construct(int $meta = 0){
        parent::__construct(self::BEACON, $meta, "Beacon");
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        Tile::createTile(TileIdentifiers::BEACON, $this->getLevel(), BeaconTile::createNBT($this));
        return true;
    }

    public function onActivate(Item $item, Player $player = null): bool{
        if($player !== null){
            if(!$this->getLevel()->getTile($this) instanceof BeaconTile){
                Tile::createTile(TileIdentifiers::BEACON, $this->getLevel(), BeaconTile::createNBT($this));
            }
            $player->addWindow(new BeaconInventory($this));
        }
        return false;
    }

    public function getHardness(): float{
        return 3;
    }

    public function getFlammability(): int{
        return 15;
    }
}