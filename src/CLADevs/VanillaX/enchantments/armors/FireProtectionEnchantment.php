<?php

namespace CLADevs\VanillaX\enchantments\armors;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\ProtectionEnchantment;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;

class FireProtectionEnchantment extends ProtectionEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(EnchantmentIds::FIRE_PROTECTION, "%enchantment.protect.fire", Rarity::UNCOMMON, ItemFlags::ARMOR, ItemFlags::NONE, 4, 1.25, [
            EntityDamageEvent::CAUSE_FIRE,
            EntityDamageEvent::CAUSE_FIRE_TICK,
            EntityDamageEvent::CAUSE_LAVA
        ]);
    }

    public function getIncompatibles(): array{
        return [EnchantmentIds::BLAST_PROTECTION, EnchantmentIds::PROJECTILE_PROTECTION, EnchantmentIds::PROTECTION];
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Armor;
    }
}