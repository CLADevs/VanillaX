<?php

namespace CLADevs\VanillaX\enchantments\types;

use CLADevs\VanillaX\enchantments\EnchantmentTrait;
use CLADevs\VanillaX\enchantments\VanillaEnchantment;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\item\Sword;
use pocketmine\item\Tool;
use pocketmine\item\ToolTier;
use pocketmine\lang\KnownTranslationFactory;

class EfficiencyEnchantment extends Enchantment implements VanillaEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_digging(), Rarity::COMMON, ItemFlags::DIG, ItemFlags::SHEARS, 5);
    }

    public function getId(): string{
        return "efficiency";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::EFFICIENCY;
    }

    public function getMinCost(int $level): int{
        return 1 + 10 * ($level - 1);
    }

    public function getMaxCost(int $level): int{
        return $this->getMinCost($level) + 50;
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Tool && !$item instanceof Sword;
    }
}