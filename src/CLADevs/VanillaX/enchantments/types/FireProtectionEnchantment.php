<?php

namespace CLADevs\VanillaX\enchantments\types;

use CLADevs\VanillaX\enchantments\EnchantmentTrait;
use CLADevs\VanillaX\enchantments\VanillaEnchantment;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\ProtectionEnchantment;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\lang\KnownTranslationFactory;

class FireProtectionEnchantment extends ProtectionEnchantment implements VanillaEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_protect_fire(), Rarity::UNCOMMON, ItemFlags::ARMOR, ItemFlags::NONE, 4, 1.25, [
            EntityDamageEvent::CAUSE_FIRE,
            EntityDamageEvent::CAUSE_FIRE_TICK,
            EntityDamageEvent::CAUSE_LAVA
        ]);
    }

    public function getId(): string{
        return "fire_protection";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::FIRE_PROTECTION;
    }

    public function getMinCost(int $level): int{
        return 10 + ($level - 1) * 8;
    }

    public function getMaxCost(int $level): int{
        return $this->getMinCost($level) + 8;
    }

    public function getIncompatibles(): array{
        return [EnchantmentIds::BLAST_PROTECTION, EnchantmentIds::PROJECTILE_PROTECTION, EnchantmentIds::PROTECTION];
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Armor;
    }
}