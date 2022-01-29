<?php

namespace CLADevs\VanillaX\enchantments\armors\fishingrod;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\FishingRod;
use pocketmine\item\Item;
use pocketmine\lang\KnownTranslationFactory;

class LuckOfTheSeaEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_lootBonusFishing(), Rarity::RARE, ItemFlags::FISHING_ROD, ItemFlags::NONE, 3);
    }

    public function getId(): string{
        return "luck_of_the_sea";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::LUCK_OF_THE_SEA;
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof FishingRod;
    }
}