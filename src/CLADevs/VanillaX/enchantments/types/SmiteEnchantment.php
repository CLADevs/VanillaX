<?php

namespace CLADevs\VanillaX\enchantments\types;

use pocketmine\item\enchantment\Enchantment;

class SmiteEnchantment extends Enchantment{

    public function __construct(){
        parent::__construct(self::SMITE, "Smite", self::RARITY_RARE, self::SLOT_SWORD, self::SLOT_AXE, 5);
    }
}