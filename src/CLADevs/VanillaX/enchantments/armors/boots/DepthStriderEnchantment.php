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

class DepthStriderEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(EnchantmentIds::DEPTH_STRIDER, "%enchantment.waterWalker", Rarity::RARE, ItemFlags::FEET, ItemFlags::NONE, 3);
    }

    public function getIncompatibles(): array{
        return [EnchantmentIds::FROST_WALKER];
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Armor && $item->getArmorSlot() === ArmorInventory::SLOT_FEET;
    }
}