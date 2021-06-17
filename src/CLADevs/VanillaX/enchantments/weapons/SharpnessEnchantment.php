<?php

namespace CLADevs\VanillaX\enchantments\weapons;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\SharpnessEnchantment as PMSharpnessEnchantment;

class SharpnessEnchantment extends PMSharpnessEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::SHARPNESS, "%enchantment.damage.all", self::RARITY_COMMON, self::SLOT_SWORD, self::SLOT_AXE, 5);
    }

    public function getIncompatibles(): array{
        return [self::BANE_OF_ARTHROPODS, self::SMITE];
    }
}