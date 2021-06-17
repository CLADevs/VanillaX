<?php

namespace CLADevs\VanillaX\enchantments\armors;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;

class BindingEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::BINDING, "%enchantment.curse.binding", self::RARITY_MYTHIC, self::SLOT_ARMOR, self::SLOT_ELYTRA, 1);
    }
}