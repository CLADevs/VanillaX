<?php

namespace CLADevs\VanillaX\enchantments\tools;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;

class SilkTouchEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::SILK_TOUCH, "%enchantment.untouching", self::RARITY_MYTHIC, self::SLOT_DIG, self::SLOT_SHEARS, 1);
    }

    public function getIncompatibles(): array{
        return [self::FORTUNE];
    }
}