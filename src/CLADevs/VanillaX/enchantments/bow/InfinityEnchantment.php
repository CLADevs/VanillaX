<?php

namespace CLADevs\VanillaX\enchantments\bow;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;

class InfinityEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::INFINITY, "%enchantment.arrowInfinite", self::RARITY_MYTHIC, self::SLOT_BOW, self::SLOT_NONE, 1);
    }

    public function getIncompatibles(): array{
        return [self::MENDING];
    }
}