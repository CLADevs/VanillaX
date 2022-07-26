<?php

namespace CLADevs\VanillaX\enchantments\types;

use CLADevs\VanillaX\enchantments\EnchantmentTrait;
use CLADevs\VanillaX\enchantments\VanillaEnchantment;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\lang\KnownTranslationFactory;

class BindingEnchantment extends Enchantment implements VanillaEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_curse_binding(), Rarity::MYTHIC, ItemFlags::ARMOR, ItemFlags::ELYTRA, 1);
    }

    public function getId(): string{
        return "binding";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::BINDING;
    }

    public function isTreasure(): bool{
        return true;
    }

    public function getMinCost(int $level): int{
        return 25;
    }

    public function getMaxCost(int $level): int{
        return 50;
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Armor;
    }
}