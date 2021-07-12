<?php

namespace CLADevs\VanillaX\enchantments\armors;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\ProtectionEnchantment;
use pocketmine\item\Item;
use pocketmine\item\enchantment\Rarity;

class BlastProtectionEnchantment extends ProtectionEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(EnchantmentIds::BLAST_PROTECTION, "%enchantment.protect.explosion", Rarity::RARE, ItemFlags::ARMOR, ItemFlags::NONE, 4, 1.5, [
            EntityDamageEvent::CAUSE_BLOCK_EXPLOSION,
            EntityDamageEvent::CAUSE_ENTITY_EXPLOSION
        ]);
    }

    public function getIncompatibles(): array{
        return [EnchantmentIds::FIRE_PROTECTION, EnchantmentIds::PROJECTILE_PROTECTION, EnchantmentIds::PROTECTION];
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Armor;
    }
}