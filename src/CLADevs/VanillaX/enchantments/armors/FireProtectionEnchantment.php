<?php

namespace CLADevs\VanillaX\enchantments\armors;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\ProtectionEnchantment;
use pocketmine\item\Item;

class FireProtectionEnchantment extends ProtectionEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::FIRE_PROTECTION, "%enchantment.protect.fire", self::RARITY_UNCOMMON, self::SLOT_ARMOR, self::SLOT_NONE, 4, 1.25, [
            EntityDamageEvent::CAUSE_FIRE,
            EntityDamageEvent::CAUSE_FIRE_TICK,
            EntityDamageEvent::CAUSE_LAVA
        ]);
    }

    public function getIncompatibles(): array{
        return [self::BLAST_PROTECTION, self::PROJECTILE_PROTECTION, self::PROTECTION];
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Armor;
    }
}