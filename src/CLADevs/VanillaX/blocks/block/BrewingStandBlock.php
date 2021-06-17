<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\BrewingStandTile;
use CLADevs\VanillaX\blocks\utils\TileUtils;
use CLADevs\VanillaX\blocks\utils\TileVanilla;
use pocketmine\block\Block;
use pocketmine\block\BrewingStand;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class BrewingStandBlock extends BrewingStand{

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        TileUtils::generateTile($this, TileVanilla::BREWING_STAND, BrewingStandTile::class);
        return true;
    }

    public function onActivate(Item $item, Player $player = null): bool{
        if($player !== null){
            $tile = $this->getLevel()->getTile($this);

            if($tile instanceof BrewingStandTile){
                $player->addWindow($tile->getInventory(), WindowTypes::BREWING_STAND);
            }
        }
        return true;
    }
}