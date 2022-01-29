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

class MultiShotEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_crossbowMultishot(), Rarity::RARE, ItemFlags::CROSSBOW, PMItemFlags::NONE, 1);
    }

    public function getId(): string{
        return "multishot";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::MULTISHOT;
    }

    public function getIncompatibles(): array{
        return [EnchantmentIds::PIERCING];
    }

    public function isItemCompatible(Item $item): bool{
        return $item->getId() === ItemIds::CROSSBOW;
    }
}