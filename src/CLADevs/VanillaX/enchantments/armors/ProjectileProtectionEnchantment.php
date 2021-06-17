<?php

namespace CLADevs\VanillaX\enchantments\armors;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\ProtectionEnchantment;

class ProjectileProtectionEnchantment extends ProtectionEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::PROJECTILE_PROTECTION, "%enchantment.protect.projectile", self::RARITY_UNCOMMON, self::SLOT_ARMOR, self::SLOT_NONE, 4, 1.5, [
            EntityDamageEvent::CAUSE_PROJECTILE
        ]);
    }

    public function getIncompatibles(): array{
        return [self::BLAST_PROTECTION, self::FIRE_PROTECTION, self::PROTECTION];
    }
}