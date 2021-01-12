<?php

namespace CLADevs\VanillaX\entities\loot\functions;

use CLADevs\VanillaX\entities\loot\LootFunction;
use pocketmine\item\Item;
use pocketmine\Server;

class FurnaceSmeltFunction extends LootFunction{

    const NAME = "furnace_smelt";

    public function apply(Item $item): void{
        /** This prob doesnt work right now */
        foreach(Server::getInstance()->getCraftingManager()->getFurnaceRecipes() as $furnace){
            if($furnace->getInput()->getId() === $item->getId()){
                $item = $furnace->getResult();
            }
        }
    }
}