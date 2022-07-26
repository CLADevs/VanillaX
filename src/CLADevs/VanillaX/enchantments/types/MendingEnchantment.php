<?php

namespace CLADevs\VanillaX\enchantments\types;

use CLADevs\VanillaX\enchantments\EnchantmentTrait;
use CLADevs\VanillaX\enchantments\VanillaEnchantment;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\lang\KnownTranslationFactory;

class MendingEnchantment extends Enchantment implements VanillaEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_mending(), Rarity::RARE, ItemFlags::NONE, ItemFlags::ALL, 1);
    }

    public function getId(): string{
        return "mending";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::MENDING;
    }

    public function isTreasure(): bool{
        return true;
    }

    public function getMinCost(int $level): int{
        return $level * 25;
    }

    public function getMaxCost(int $level): int{
        return $this->getMinCost($level) + 50;
    }

    public function getIncompatibles(): array{
        return [EnchantmentIds::INFINITY];
    }
}