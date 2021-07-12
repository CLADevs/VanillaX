<?php

namespace CLADevs\VanillaX\enchantments\armors;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\Armor;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;

class ThornEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(EnchantmentIds::THORNS, "%enchantment.thorns", Rarity::MYTHIC, ItemFlags::TORSO, ItemFlags::HEAD | ItemFlags::LEGS | ItemFlags::FEET, 3);
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Armor;
    }
}