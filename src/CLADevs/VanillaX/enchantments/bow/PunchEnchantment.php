<?php

namespace CLADevs\VanillaX\enchantments\bow;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;

class PunchEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::PUNCH, "%enchantment.arrowKnockback", self::RARITY_RARE, self::SLOT_BOW, self::SLOT_NONE, 2);
    }
}