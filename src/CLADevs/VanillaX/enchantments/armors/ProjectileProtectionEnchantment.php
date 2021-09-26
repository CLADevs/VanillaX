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
use pocketmine\lang\KnownTranslationFactory;

class ProjectileProtectionEnchantment extends ProtectionEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_protect_projectile(), Rarity::UNCOMMON, ItemFlags::ARMOR, ItemFlags::NONE, 4, 1.5, [
            EntityDamageEvent::CAUSE_PROJECTILE
        ]);
    }

    public function getId(): string{
        return "projectile_protection";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::PROJECTILE_PROTECTION;
    }

    public function getIncompatibles(): array{
        return [EnchantmentIds::BLAST_PROTECTION, EnchantmentIds::FIRE_PROTECTION, EnchantmentIds::PROTECTION];
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Armor;
    }
}