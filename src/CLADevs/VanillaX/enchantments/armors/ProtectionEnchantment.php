<?php

namespace CLADevs\VanillaX\enchantments\armors;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\ProtectionEnchantment as PMProtectionEnchantment;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;

class ProtectionEnchantment extends PMProtectionEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(EnchantmentIds::PROTECTION, "%enchantment.protect.all", Rarity::COMMON, ItemFlags::ARMOR, ItemFlags::NONE, 4, 0.75, null);
    }

    public function getIncompatibles(): array{
        return [EnchantmentIds::BLAST_PROTECTION, EnchantmentIds::FIRE_PROTECTION, EnchantmentIds::PROJECTILE_PROTECTION];
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Armor;
    }
}