<?php

namespace CLADevs\VanillaX\enchantments\types;

use pocketmine\item\enchantment\Enchantment;

class LootingEnchantment extends Enchantment{

    public function __construct(){
        parent::__construct(self::LOOTING, "Looting", self::RARITY_RARE, self::SLOT_SWORD, self::SLOT_NONE, 3);
    }
}