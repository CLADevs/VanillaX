<?php

namespace CLADevs\VanillaX\enchantments\armors;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\item\Armor;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\lang\KnownTranslationFactory;

class ThornEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_thorns(), Rarity::MYTHIC, ItemFlags::TORSO, ItemFlags::HEAD | ItemFlags::LEGS | ItemFlags::FEET, 3);
    }

    public function getId(): string{
        return "thorns";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::THORNS;
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Armor;
    }
}