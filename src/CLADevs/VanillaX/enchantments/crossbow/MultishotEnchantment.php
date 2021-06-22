<?php

namespace CLADevs\VanillaX\enchantments\crossbow;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;

class MultishotEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        //TODO
        parent::__construct(self::MULTISHOT, "%enchantment.crossbowMultishot", self::RARITY_RARE, self::$SLOT_CROSSBOW, self::SLOT_NONE, 1);
    }

    public function getIncompatibles(): array{
        return [self::PIERCING];
    }

    public function isItemCompatible(Item $item): bool{
        return $item->getId() === ItemIds::CROSSBOW;
    }
}