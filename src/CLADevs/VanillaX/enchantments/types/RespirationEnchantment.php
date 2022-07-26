<?php

namespace CLADevs\VanillaX\enchantments\types;

use CLADevs\VanillaX\enchantments\EnchantmentTrait;
use CLADevs\VanillaX\enchantments\VanillaEnchantment;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\lang\KnownTranslationFactory;

class RespirationEnchantment extends Enchantment implements VanillaEnchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_oxygen(), Rarity::RARE, ItemFlags::HEAD, ItemFlags::NONE, 3);
    }

    public function getId(): string{
        return "respiration";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::RESPIRATION;
    }

    public function getMinCost(int $level): int{
        return $level * 10;
    }

    public function getMaxCost(int $level): int{
        return $this->getMinCost($level) + 30;
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Armor && $item->getArmorSlot() === ArmorInventory::SLOT_HEAD;
    }
}