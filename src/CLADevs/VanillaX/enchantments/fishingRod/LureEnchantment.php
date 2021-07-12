<?php

namespace CLADevs\VanillaX\enchantments\fishingRod;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;

class LureEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        //TODO
        parent::__construct(self::LURE, "%enchantment.fishingSpeed", self::RARITY_RARE, self::SLOT_FISHING_ROD, self::SLOT_NONE, 3);
    }

    public function isItemCompatible(Item $item): bool{
        return $item->getId() === ItemIds::FISHING_ROD;
    }
}