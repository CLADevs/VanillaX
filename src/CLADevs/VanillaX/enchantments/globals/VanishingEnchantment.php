<?php

namespace CLADevs\VanillaX\enchantments\globals;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;

class VanishingEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::VANISHING, "%enchantment.curse.vanishing", self::RARITY_MYTHIC, self::SLOT_NONE, self::SLOT_ALL, 1);
    }

    public function isTreasure(): bool{
        return true;
    }
}