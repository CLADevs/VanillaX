<?php

namespace CLADevs\VanillaX\enchantments\armors\boots;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;

class FrostWalkerEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(EnchantmentIds::FROST_WALKER, "%enchantment.frostwalker", Rarity::MYTHIC, ItemFlags::FEET, ItemFlags::NONE, 2);
    }

    public function isTreasure(): bool{
        return true;
    }

    public function getIncompatibles(): array{
        return [EnchantmentIds::DEPTH_STRIDER];
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Armor && $item->getArmorSlot() === ArmorInventory::SLOT_FEET;
    }
}