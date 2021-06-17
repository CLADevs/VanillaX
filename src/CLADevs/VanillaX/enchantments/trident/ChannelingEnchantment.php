<?php

namespace CLADevs\VanillaX\enchantments\trident;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;

class ChannelingEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::CHANNELING, "%enchantment.tridentChanneling", self::RARITY_MYTHIC, self::SLOT_TRIDENT, self::SLOT_NONE, 1);
    }

    public function getIncompatibles(): array{
        return [self::RIPTIDE];
    }
}