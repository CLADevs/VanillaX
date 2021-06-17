<?php

namespace CLADevs\VanillaX\enchantments\armors\helmets;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\enchantment\Enchantment;

class AquaAffinityEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::AQUA_AFFINITY, "%enchantment.waterWorker", self::RARITY_RARE, self::SLOT_HEAD, self::SLOT_NONE, 1);
    }
}