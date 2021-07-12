<?php

namespace CLADevs\VanillaX\enchantments\crossbow;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;

class PiercingEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        //TODO
        parent::__construct(self::PIERCING, "%enchantment.crossbowPiercing", self::RARITY_COMMON, self::$SLOT_CROSSBOW, self::SLOT_NONE, 4);
    }

    public function getIncompatibles(): array{
        return [self::MULTISHOT];
    }

    public function isItemCompatible(Item $item): bool{
        return $item->getId() === ItemIds::CROSSBOW;
    }
}