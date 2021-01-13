<?php

namespace CLADevs\VanillaX\enchantments\types;

use pocketmine\item\enchantment\Enchantment;

class FortuneEnchantment extends Enchantment{

    public function __construct(){
        parent::__construct(self::FORTUNE, "Fortune", self::RARITY_RARE, self::SLOT_DIG, self::SLOT_SHEARS, 3);
    }
}