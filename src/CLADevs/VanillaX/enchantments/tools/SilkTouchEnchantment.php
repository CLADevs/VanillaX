<?php

namespace CLADevs\VanillaX\enchantments\tools;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\lang\KnownTranslationFactory;

class SilkTouchEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_untouching(), Rarity::MYTHIC, ItemFlags::DIG, ItemFlags::SHEARS, 1);
    }

    public function getId(): string{
        return "silk_touch";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::SILK_TOUCH;
    }

    public function getIncompatibles(): array{
        return [EnchantmentIds::FORTUNE];
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Tool;
    }
}