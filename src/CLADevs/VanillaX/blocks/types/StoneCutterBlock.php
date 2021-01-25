<?php

namespace CLADevs\VanillaX\blocks\types;

use CLADevs\VanillaX\blocks\TileIdentifiers;
use CLADevs\VanillaX\blocks\tiles\StoneCutterTile;
use CLADevs\VanillaX\items\utils\NonAutomaticCallItemTrait;
use pocketmine\block\Block;
use pocketmine\block\Stonecutter;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Tile;

class StoneCutterBlock extends Stonecutter implements NonAutomaticCallItemTrait{

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        Tile::createTile(TileIdentifiers::STONECUTTER, $this->getLevel(), StoneCutterTile::createNBT($this));
        return true;
    }

    public function onActivate(Item $item, Player $player = null): bool{
        if($player !== null){
            /** @var StoneCutterTile|null $tile */
            $tile = $this->getLevel()->getTile($this);

            if($tile === null){
                $tile = Tile::createTile(TileIdentifiers::STONECUTTER, $this->getLevel(), StoneCutterTile::createNBT($this));
            }
            if($tile instanceof StoneCutterTile){
                $player->addWindow($tile->getInventory());
            }
        }
        return true;
    }
}