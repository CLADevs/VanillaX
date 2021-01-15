<?php

namespace CLADevs\VanillaX\entities\utils\trade\professions;

use CLADevs\VanillaX\entities\utils\trade\VillagerOffer;
use CLADevs\VanillaX\entities\utils\trade\VillagerProfession;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class ArmorerProfession extends VillagerProfession{

    public function __construct(){
        //TODO Blast Furnace
        parent::__construct(self::ARMORER, "Armorer");
    }

    public function getNovice(): array{
        $offers = [];
        $i = new VillagerOffer(2, 0.05, 16);
        $i->setInput(ItemFactory::get(ItemIds::COAL)->setCount(15));
        $i->setResult(ItemFactory::get(ItemIds::EMERALD)->setCount(1));
        $offers[] = $i;

        foreach([ItemIds::IRON_HELMET => 5, ItemIds::IRON_CHESTPLATE => 9, ItemIds::IRON_LEGGINGS => 7, ItemIds::IRON_BOOTS => 4] as $id => $count){
            $i = new VillagerOffer(1, 0.02, 12);
            $i->setInput(ItemFactory::get(ItemIds::EMERALD)->setCount($count));
            $i->setResult($id);
            $offers[] = $i;
        }
        return $offers;
    }
}