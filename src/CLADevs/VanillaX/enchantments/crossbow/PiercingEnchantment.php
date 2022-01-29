<?php

namespace CLADevs\VanillaX\enchantments\crossbow;

use CLADevs\VanillaX\enchantments\ItemFlags;
use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags as PMItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\lang\KnownTranslationFactory;

class PiercingEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_crossbowPiercing(), Rarity::COMMON, ItemFlags::CROSSBOW, PMItemFlags::NONE, 4);
    }

    public function getId(): string{
        return "piercing";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::PIERCING;
    }

    public function getIncompatibles(): array{
        return [EnchantmentIds::MULTISHOT];
    }

    public function isItemCompatible(Item $item): bool{
        return $item->getId() === ItemIds::CROSSBOW;
    }
}