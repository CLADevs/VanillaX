<?php

namespace CLADevs\VanillaX\enchantments\trident;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;

class LoyaltyEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::LOYALTY, "%enchantment.tridentLoyalty", self::RARITY_RARE, self::SLOT_TRIDENT, self::SLOT_NONE, 5);
    }

    public function getIncompatibles(): array{
        return [self::RIPTIDE];
    }

    public function isItemCompatible(Item $item): bool{
        return $item->getId() === ItemIds::TRIDENT;
    }
}