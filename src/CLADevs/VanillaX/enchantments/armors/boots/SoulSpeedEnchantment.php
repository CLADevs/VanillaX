<?php

namespace CLADevs\VanillaX\enchantments\armors\boots;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;

class SoulSpeedEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::SOUL_SPEED, "%enchantment.soul_speed", self::RARITY_MYTHIC, self::SLOT_FEET, self::SLOT_NONE, 3);
    }
}