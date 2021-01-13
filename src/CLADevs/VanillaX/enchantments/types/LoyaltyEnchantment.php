<?php

namespace CLADevs\VanillaX\enchantments\types;

use pocketmine\item\enchantment\Enchantment;

class LoyaltyEnchantment extends Enchantment{

    public function __construct(){
        parent::__construct(self::LOYALTY, "Loyalty", self::RARITY_RARE, self::SLOT_TRIDENT, self::SLOT_NONE, 3);
    }
}