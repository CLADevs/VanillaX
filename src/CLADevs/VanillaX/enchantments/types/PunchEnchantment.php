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

class PunchEnchantment extends Enchantment implements VanillaEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_arrowKnockback(), Rarity::RARE, ItemFlags::BOW, ItemFlags::NONE, 2);
    }

    public function getId(): string{
        return "punch";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::PUNCH;
    }

    public function getMinCost(int $level): int{
        return 12 + ($level - 1) * 20;
    }

    public function getMaxCost(int $level): int{
        return $this->getMinCost($level) + 25;
    }

    public function isItemCompatible(Item $item): bool{
        return $item->getId() === ItemIds::BOW;
    }
}