<?php

namespace CLADevs\VanillaX\enchantments\armors\boots;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;

class FrostWalkerEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::FROST_WALKER, "%enchantment.frostwalker", self::RARITY_MYTHIC, self::SLOT_FEET, self::SLOT_NONE, 2);
    }

    public function isTreasure(): bool{
        return true;
    }

    public function getIncompatibles(): array{
        return [self::DEPTH_STRIDER];
    }
}