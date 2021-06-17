<?php

namespace CLADevs\VanillaX\enchantments\fishingRod;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;

class LuckOfTheSeaEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        //TODO
        parent::__construct(self::LUCK_OF_THE_SEA, "%enchantment.lootBonusFishing", self::RARITY_RARE, self::SLOT_FISHING_ROD, self::SLOT_NONE, 3);
    }
}