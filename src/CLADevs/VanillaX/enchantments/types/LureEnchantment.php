<?php

namespace CLADevs\VanillaX\enchantments\types;

use pocketmine\item\enchantment\Enchantment;

class LureEnchantment extends Enchantment{

    public function __construct(){
        parent::__construct(self::LURE, "Lure", self::RARITY_RARE, self::SLOT_FISHING_ROD, self::SLOT_NONE, 3);
    }
}