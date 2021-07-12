<?php

namespace CLADevs\VanillaX\enchantments\trident;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;

class RiptideEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::RIPTIDE, "%enchantment.tridentRiptide", self::RARITY_RARE, self::SLOT_TRIDENT, self::SLOT_NONE, 3);
    }

    public function getIncompatibles(): array{
        return [self::LOYALTY, self::CHANNELING];
    }

    public function isItemCompatible(Item $item): bool{
        return $item->getId() === ItemIds::TRIDENT;
    }
}