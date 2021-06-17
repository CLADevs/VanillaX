<?php

namespace CLADevs\VanillaX\enchantments\armors\boots;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\ProtectionEnchantment;

class FeatherFallingEnchantment extends ProtectionEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::FEATHER_FALLING, "%enchantment.protect.fall", self::RARITY_UNCOMMON, self::SLOT_FEET, self::SLOT_NONE, 4, 2.5, [
            EntityDamageEvent::CAUSE_FALL
        ]);
    }
}