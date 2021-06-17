<?php

namespace CLADevs\VanillaX\enchantments\armors;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\ProtectionEnchantment as PMProtectionEnchantment;

class ProtectionEnchantment extends PMProtectionEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::PROTECTION, "%enchantment.protect.all", self::RARITY_COMMON, self::SLOT_ARMOR, self::SLOT_NONE, 4, 0.75, null);
    }

    public function getIncompatibles(): array{
        return [self::BLAST_PROTECTION, self::FIRE_PROTECTION, self::PROJECTILE_PROTECTION];
    }
}