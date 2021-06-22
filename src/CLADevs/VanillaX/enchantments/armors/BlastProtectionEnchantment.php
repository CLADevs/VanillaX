<?php

namespace CLADevs\VanillaX\enchantments\armors;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\ProtectionEnchantment;
use pocketmine\item\Item;

class BlastProtectionEnchantment extends ProtectionEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(self::BLAST_PROTECTION, "%enchantment.protect.explosion", self::RARITY_RARE, self::SLOT_ARMOR, self::SLOT_NONE, 4, 1.5, [
            EntityDamageEvent::CAUSE_BLOCK_EXPLOSION,
            EntityDamageEvent::CAUSE_ENTITY_EXPLOSION
        ]);
    }

    public function getIncompatibles(): array{
        return [self::FIRE_PROTECTION, self::PROJECTILE_PROTECTION, self::PROTECTION];
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Armor;
    }
}