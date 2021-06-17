<?php

namespace CLADevs\VanillaX\enchantments\armors;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;

class ThornEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::THORNS, "%enchantment.thorns", self::RARITY_MYTHIC, self::SLOT_TORSO, self::SLOT_HEAD | self::SLOT_LEGS | self::SLOT_FEET, 3);
    }
}