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
use pocketmine\lang\KnownTranslationFactory;

class LootingEnchantment extends Enchantment implements VanillaEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_lootBonus(), Rarity::RARE, ItemFlags::SWORD, ItemFlags::NONE, 3);
    }

    public function getId(): string{
        return "looting";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::LOOTING;
    }

    public function getMinCost(int $level): int{
        return 15 * ($level - 1) * 9;
    }

    public function getMaxCost(int $level): int{
        return $this->getMinCost($level) + 50;
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Sword;
    }
}