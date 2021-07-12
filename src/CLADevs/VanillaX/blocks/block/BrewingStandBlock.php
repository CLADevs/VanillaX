<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\BrewingStandTile;
use pocketmine\block\BrewingStand;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class BrewingStandBlock extends BrewingStand{
    //TODO tile

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
        if($player !== null){
            $tile = $this->getPos()->getWorld()->getTile($this->getPos());

            if($tile instanceof BrewingStandTile){
                $player->setCurrentWindow($tile->getInventory());
            }
        }
        return true;
    }
}