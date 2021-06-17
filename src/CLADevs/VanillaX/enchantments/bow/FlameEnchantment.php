<?php

namespace CLADevs\VanillaX\enchantments\bow;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;

class FlameEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::FLAME, "%enchantment.arrowFire", self::RARITY_RARE, self::SLOT_BOW, self::SLOT_NONE, 1);
    }
}