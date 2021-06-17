<?php

namespace CLADevs\VanillaX\enchantments\globals;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;

class MendingEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::MENDING, "%enchantment.mending", self::RARITY_RARE, self::SLOT_NONE, self::SLOT_ALL, 1);
    }

    public function getIncompatibles(): array{
        return [self::INFINITY];
    }
}