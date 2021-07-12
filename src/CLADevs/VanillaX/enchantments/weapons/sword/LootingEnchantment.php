<?php

namespace CLADevs\VanillaX\enchantments\weapons\sword;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\item\Sword;

class LootingEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(EnchantmentIds::LOOTING, "%enchantment.lootBonus", Rarity::RARE, ItemFlags::SWORD, ItemFlags::NONE, 3);
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Sword;
    }
}