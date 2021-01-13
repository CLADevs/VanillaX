<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class HorseArmorItem extends Item{

    public function __construct(int $id, int $meta = 0, string $name = "Unknown"){
        switch($id){
            case self::LEATHER_HORSE_ARMOR:
                $name = "Leather Horse Armor";
                break;
            case self::IRON_HORSE_ARMOR:
                $name = "Iron Horse Armor";
                break;
            case self::GOLD_HORSE_ARMOR:
                $name = "Gold Horse Armor";
                break;
            case self::DIAMOND_HORSE_ARMOR:
                $name = "Diamond Horse Armor";
                break;
        }
        parent::__construct($id, $meta, $name);
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}