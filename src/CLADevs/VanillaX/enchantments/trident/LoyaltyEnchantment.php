<?php

namespace CLADevs\VanillaX\enchantments\trident;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;

class LoyaltyEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::LOYALTY, "%enchantment.tridentLoyalty", self::RARITY_RARE, self::SLOT_TRIDENT, self::SLOT_NONE, 5);
    }

    public function getIncompatibles(): array{
        return [self::RIPTIDE];
    }
}