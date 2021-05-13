<?php

namespace CLADevs\VanillaX\blocks\types;

use CLADevs\VanillaX\blocks\tiles\BrewingStandTile;
use pocketmine\block\Block;
use pocketmine\block\BrewingStand;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;
use pocketmine\tile\Tile;

class BrewingStandBlock extends BrewingStand{

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        Tile::createTile(Tile::BREWING_STAND, $this->getLevel(), BrewingStandTile::createNBT($this));
        return true;
    }

    public function onActivate(Item $item, Player $player = null): bool{
        if($player !== null){
            $tile = $this->getLevel()->getTile($this);

            if(!$tile instanceof BrewingStandTile){
                $tile = Tile::createTile(Tile::BREWING_STAND, $this->getLevel(), BrewingStandTile::createNBT($this));
            }
            $player->addWindow($tile->getInventory(), WindowTypes::BREWING_STAND);
        }
        return true;
    }
}