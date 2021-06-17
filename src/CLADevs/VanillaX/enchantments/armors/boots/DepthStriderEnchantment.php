<?php

namespace CLADevs\VanillaX\enchantments\armors\boots;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;

class DepthStriderEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::DEPTH_STRIDER, "%enchantment.waterWalker", self::RARITY_RARE, self::SLOT_FEET, self::SLOT_NONE, 3);
    }

    public function getIncompatibles(): array{
        return [self::FROST_WALKER];
    }
}