<?php

namespace CLADevs\VanillaX\enchantments\armors\boots;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use CLADevs\VanillaX\items\ItemManager;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Item;

class DepthStriderEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::DEPTH_STRIDER, "%enchantment.waterWalker", self::RARITY_RARE, self::SLOT_FEET, self::SLOT_NONE, 3);
    }

    public function getIncompatibles(): array{
        return [self::FROST_WALKER];
    }

    public function isItemCompatible(Item $item): bool{
        return in_array($item->getId(), ItemManager::getBootsList());
    }
}