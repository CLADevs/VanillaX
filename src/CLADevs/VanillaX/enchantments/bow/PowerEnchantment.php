<?php

namespace CLADevs\VanillaX\enchantments\bow;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;

class PowerEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::POWER, "%enchantment.arrowDamage", self::RARITY_COMMON, self::SLOT_BOW, self::SLOT_NONE, 5);
    }
}