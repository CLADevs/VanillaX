<?php

namespace CLADevs\VanillaX\enchantments\types;

use pocketmine\item\enchantment\Enchantment;

class LuckOfTheSeaEnchantment extends Enchantment{

    public function __construct(){
        parent::__construct(self::LUCK_OF_THE_SEA, "Luck of the Sea", self::RARITY_RARE, self::SLOT_FISHING_ROD, self::SLOT_NONE, 3);
    }
}