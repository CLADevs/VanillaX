<?php

namespace CLADevs\VanillaX\enchantments\weapons;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\Axe;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\Sword;

class SmiteEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::SMITE, "%enchantment.damage.undead", self::RARITY_UNCOMMON, self::SLOT_SWORD, self::SLOT_AXE, 5);
    }

    public function getIncompatibles(): array{
        return [self::BANE_OF_ARTHROPODS, self::SHARPNESS];
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Sword || $item instanceof Axe;
    }
}