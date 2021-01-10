<?php

namespace CLADevs\VanillaX\blocks\types;

use CLADevs\VanillaX\inventories\AnvilInventory;
use pocketmine\block\Anvil;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class AnvilBlock extends Anvil{

    public function onActivate(Item $item, Player $player = null): bool{
        if($player !== null){
            $player->addWindow(new AnvilInventory($this), WindowTypes::ANVIL);
        }
        return true;
    }
}