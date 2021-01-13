<?php

namespace CLADevs\VanillaX\enchantments\types;

use pocketmine\item\enchantment\Enchantment;

class DepthStriderEnchantment extends Enchantment{

    public function __construct(){
        parent::__construct(self::DEPTH_STRIDER, "Depth Strider", self::RARITY_RARE, self::SLOT_FEET, self::SLOT_NONE, 3);
    }
}