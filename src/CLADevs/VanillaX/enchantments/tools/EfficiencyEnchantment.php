<?php

namespace CLADevs\VanillaX\enchantments\tools;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\Tool;

class EfficiencyEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::EFFICIENCY, "%enchantment.digging", self::RARITY_COMMON, self::SLOT_DIG, self::SLOT_SHEARS, 5);
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Tool;
    }
}