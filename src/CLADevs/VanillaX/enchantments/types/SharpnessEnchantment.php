<?php

namespace CLADevs\VanillaX\enchantments\types;

use CLADevs\VanillaX\enchantments\EnchantmentTrait;
use CLADevs\VanillaX\enchantments\VanillaEnchantment;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Axe;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\item\Sword;
use pocketmine\item\enchantment\SharpnessEnchantment as PMSharpnessEnchantment;
use pocketmine\lang\KnownTranslationFactory;

class SharpnessEnchantment extends PMSharpnessEnchantment implements VanillaEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_damage_all(), Rarity::COMMON, ItemFlags::SWORD, ItemFlags::AXE, 5);
    }

    public function getId(): string{
        return "sharpness";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::SHARPNESS;
    }

    public function getMinCost(int $level): int{
        return 1 + ($level - 1) * 11;
    }

    public function getMaxCost(int $level): int{
        return $this->getMinCost($level) + 20;
    }

    public function getIncompatibles(): array{
        return [EnchantmentIds::BANE_OF_ARTHROPODS, EnchantmentIds::SMITE];
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Sword || $item instanceof Axe;
    }
}