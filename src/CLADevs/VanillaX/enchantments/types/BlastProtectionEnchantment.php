<?php

namespace CLADevs\VanillaX\enchantments\types;

use CLADevs\VanillaX\enchantments\EnchantmentTrait;
use CLADevs\VanillaX\enchantments\VanillaEnchantment;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\ProtectionEnchantment;
use pocketmine\item\Item;
use pocketmine\item\enchantment\Rarity;
use pocketmine\lang\KnownTranslationFactory;

class BlastProtectionEnchantment extends ProtectionEnchantment implements VanillaEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_protect_explosion(), Rarity::RARE, ItemFlags::ARMOR, ItemFlags::NONE, 4, 1.5, [
            EntityDamageEvent::CAUSE_BLOCK_EXPLOSION,
            EntityDamageEvent::CAUSE_ENTITY_EXPLOSION
        ]);
    }

    public function getId(): string{
        return "blast_protection";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::BLAST_PROTECTION;
    }

    public function getMinCost(int $level): int{
        return 5 + ($level - 1) * 8;
    }

    public function getMaxCost(int $level): int{
        return $this->getMinCost($level) + 8;
    }

    public function getIncompatibles(): array{
        return [EnchantmentIds::FIRE_PROTECTION, EnchantmentIds::PROJECTILE_PROTECTION, EnchantmentIds::PROTECTION];
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Armor;
    }
}