<?php

namespace CLADevs\VanillaX\enchantments\weapons;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\Axe;
use pocketmine\item\enchantment\SharpnessEnchantment as PMSharpnessEnchantment;
use pocketmine\item\Item;
use pocketmine\item\Sword;

class SharpnessEnchantment extends PMSharpnessEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::SHARPNESS, "%enchantment.damage.all", self::RARITY_COMMON, self::SLOT_SWORD, self::SLOT_AXE, 5);
    }

    public function getIncompatibles(): array{
        return [self::BANE_OF_ARTHROPODS, self::SMITE];
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Sword || $item instanceof Axe;
    }
}