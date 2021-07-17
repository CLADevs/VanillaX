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

class SoulSpeedEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(EnchantmentIds::SOUL_SPEED, "%enchantment.soul_speed", Rarity::MYTHIC, ItemFlags::FEET, ItemFlags::NONE, 3);
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Armor && $item->getArmorSlot() === ArmorInventory::SLOT_FEET;
    }
}