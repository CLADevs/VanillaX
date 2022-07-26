<?php

namespace CLADevs\VanillaX\enchantments\types;

use CLADevs\VanillaX\enchantments\EnchantmentTrait;
use CLADevs\VanillaX\enchantments\VanillaEnchantment;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\lang\KnownTranslationFactory;

class VanishingEnchantment extends Enchantment implements VanillaEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_curse_vanishing(), Rarity::MYTHIC, ItemFlags::NONE, ItemFlags::ALL, 1);
    }

    public function getId(): string{
        return "vanishing";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::VANISHING;
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
}