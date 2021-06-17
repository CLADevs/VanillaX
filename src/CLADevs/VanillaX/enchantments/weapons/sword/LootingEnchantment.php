<?php

namespace CLADevs\VanillaX\enchantments\weapons\sword;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;

class LootingEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::LOOTING, "%enchantment.lootBonus", self::RARITY_RARE, self::SLOT_SWORD, self::SLOT_NONE, 3);
    }
}