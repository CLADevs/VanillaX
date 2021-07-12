<?php

namespace CLADevs\VanillaX\enchantments\fishingRod;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;

class LuckOfTheSeaEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        //TODO
        parent::__construct(EnchantmentIds::LUCK_OF_THE_SEA, "%enchantment.lootBonusFishing", Rarity::RARE, ItemFlags::FISHING_ROD, ItemFlags::NONE, 3);
    }

    public function isItemCompatible(Item $item): bool{
        return $item->getId() === ItemIds::FISHING_ROD;
    }
}