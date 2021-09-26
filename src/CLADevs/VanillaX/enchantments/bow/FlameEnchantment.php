<?php

namespace CLADevs\VanillaX\enchantments\bow;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\lang\KnownTranslationFactory;

class FlameEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(KnownTranslationFactory::enchantment_arrowFire(), Rarity::RARE, ItemFlags::BOW, ItemFlags::NONE, 1);
    }

    public function getId(): string{
        return "flame";
    }

    public function getMcpeId(): int{
        return EnchantmentIds::FLAME;
    }

    public function isItemCompatible(Item $item): bool{
        return $item->getId() === ItemIds::BOW;
    }
}