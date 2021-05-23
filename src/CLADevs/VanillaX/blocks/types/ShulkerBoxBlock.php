<?php

namespace CLADevs\VanillaX\blocks\types;

use CLADevs\VanillaX\blocks\TileIdentifiers;
use CLADevs\VanillaX\blocks\tiles\ShulkerBoxTile;
use CLADevs\VanillaX\items\utils\NonAutomaticCallItemTrait;
use pocketmine\block\Block;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Tile;

class ShulkerBoxBlock extends Transparent implements NonAutomaticCallItemTrait{

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        Tile::createTile(TileIdentifiers::SHULKER_BOX, $this->getLevel(), ShulkerBoxTile::createNBT($this));
        return true;
    }

    public function onActivate(Item $item, Player $player = null): bool{
        if($player !== null){
            /** @var ShulkerBoxTile|null $tile */
            $tile = $this->getLevel()->getTile($this);

            if($tile === null){
                $tile = Tile::createTile(TileIdentifiers::SHULKER_BOX, $this->getLevel(), ShulkerBoxTile::createNBT($this));
            }
            if($tile instanceof ShulkerBoxTile){
                $player->addWindow($tile->getInventory());
            }
        }
        return true;
    }

    public function getHardness(): float{
        return 2;
    }

    public function getBlastResistance(): float{
        return 6;
    }
}