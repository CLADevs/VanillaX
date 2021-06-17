<?php

namespace CLADevs\VanillaX\enchantments\armors\helmets;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;

class RespirationEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::RESPIRATION, "%enchantment.oxygen", self::RARITY_RARE, self::SLOT_HEAD, self::SLOT_NONE, 3);
    }
}