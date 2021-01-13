<?php

namespace CLADevs\VanillaX\enchantments\types;

use pocketmine\item\enchantment\Enchantment;

class ImpalingEnchantment extends Enchantment{

    public function __construct(){
        parent::__construct(self::IMPALING, "Impaling", self::RARITY_RARE, self::SLOT_TRIDENT, self::SLOT_NONE, 5);
    }
}