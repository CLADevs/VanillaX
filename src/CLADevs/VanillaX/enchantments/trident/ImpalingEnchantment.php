<?php

namespace CLADevs\VanillaX\enchantments\trident;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;

class ImpalingEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::IMPALING, "%enchantment.tridentImpaling", self::RARITY_RARE, self::SLOT_TRIDENT, self::SLOT_NONE, 5);
    }

    public function isItemCompatible(Item $item): bool{
        return $item->getId() === ItemIds::TRIDENT;
    }
}