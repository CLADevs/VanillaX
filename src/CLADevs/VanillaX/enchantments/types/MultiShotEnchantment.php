<?php

namespace CLADevs\VanillaX\enchantments\types;

use pocketmine\item\enchantment\Enchantment;

class MultiShotEnchantment extends Enchantment{

    public function __construct(){
        parent::__construct(self::MULTISHOT, "MultiShot", self::RARITY_RARE, self::SLOT_BOW, self::SLOT_NONE, 3);
    }
}