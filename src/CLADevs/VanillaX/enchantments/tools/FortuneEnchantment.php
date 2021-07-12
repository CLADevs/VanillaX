<?php

namespace CLADevs\VanillaX\enchantments\tools;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\item\Tool;

class FortuneEnchantment extends Enchantment{
    use EnchantmentTrait;

    public function __construct(){
        parent::__construct(EnchantmentIds::FORTUNE, "%enchantment.lootBonusDigger", Rarity::RARE, ItemFlags::DIG, ItemFlags::NONE, 3);
    }

    public function getIncompatibles(): array{
        return [EnchantmentIds::SILK_TOUCH];
    }

    public function isItemCompatible(Item $item): bool{
        return $item instanceof Tool;
    }
}