<?php

namespace CLADevs\VanillaX\enchantments\types;

use pocketmine\item\enchantment\Enchantment;

class FrostWalkerEnchantment extends Enchantment{

    public function __construct(){
        parent::__construct(self::FROST_WALKER, "Frost Walker", self::RARITY_RARE, self::SLOT_FEET, self::SLOT_NONE, 2);
    }
}