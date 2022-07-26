<?php

namespace CLADevs\VanillaX\enchantments\types;

use CLADevs\VanillaX\enchantments\EnchantmentTrait;
use CLADevs\VanillaX\enchantments\VanillaEnchantment;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Axe;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\item\Sword;
use pocketmine\lang\KnownTranslationFactory;

class BaneOfArthropodsEnchantment extends Enchantment implements VanillaEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_damage_arthropods(), Rarity::UNCOMMON, ItemFlags::SWORD, ItemFlags::AXE, 5);
    }

    public function getId(): string{
        return "bane_of_arthropods";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::BANE_OF_ARTHROPODS;
    }

    public function getMinCost(int $level): int{
        return 5 + ($level - 1) * 8;
    }

    public function getMaxCost(int $level): int{
        return $this->getMinCost($level) + 20;
    }

    public function getIncompatibles(): array{
        return [EnchantmentIds::SHARPNESS, EnchantmentIds::SMITE];
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Sword || $item instanceof Axe;
    }
}