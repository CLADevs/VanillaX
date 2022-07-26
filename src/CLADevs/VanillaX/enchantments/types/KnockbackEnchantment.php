<?php

namespace CLADevs\VanillaX\enchantments\types;

use CLADevs\VanillaX\enchantments\EnchantmentTrait;
use CLADevs\VanillaX\enchantments\VanillaEnchantment;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\item\Sword;
use pocketmine\item\enchantment\KnockbackEnchantment as PMKnockbackEnchantment;
use pocketmine\lang\KnownTranslationFactory;

class KnockbackEnchantment extends PMKnockbackEnchantment implements VanillaEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_knockback(), Rarity::UNCOMMON, ItemFlags::SWORD, ItemFlags::NONE, 2);
    }

    public function getId(): string{
        return "knockback";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::KNOCKBACK;
    }

    public function getMinCost(int $level): int{
        return 5 + 20 * ($level - 1);
    }

    public function getMaxCost(int $level): int{
        return $this->getMinCost($level) + 50;
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Sword;
    }
}