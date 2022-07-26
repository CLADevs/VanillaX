<?php

namespace CLADevs\VanillaX\enchantments\types;

use CLADevs\VanillaX\enchantments\EnchantmentTrait;
use CLADevs\VanillaX\enchantments\VanillaEnchantment;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\lang\KnownTranslationFactory;

class ImpalingEnchantment extends Enchantment implements VanillaEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_tridentImpaling(), Rarity::RARE, ItemFlags::TRIDENT, ItemFlags::NONE, 5);
    }

    public function getId(): string{
        return "impaling";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::IMPALING;
    }

    public function getMinCost(int $level): int{
        return 1 + ($level - 1) * 8;
    }

    public function getMaxCost(int $level): int{
        return $this->getMinCost($level) + 20;
    }

    public function isItemCompatible(Item $item): bool{
        return $item->getId() === ItemIds::TRIDENT;
    }
}