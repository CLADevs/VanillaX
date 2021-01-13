<?php

namespace CLADevs\VanillaX\enchantments\types;

use pocketmine\item\enchantment\Enchantment;

class ChannelingEnchantment extends Enchantment{

    public function __construct(){
        parent::__construct(self::CHANNELING, "Channeling", self::RARITY_RARE, self::SLOT_TRIDENT, self::SLOT_NONE, 1);
    }
}