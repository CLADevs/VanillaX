<?php

namespace CLADevs\VanillaX\blocks\block\ore;

use pocketmine\block\GoldOre;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class GoldOreBlock extends GoldOre{

    /**
     * @param Item $item
     * @return Item[]
     */
    public function getDropsForCompatibleTool(Item $item): array{
        return [ItemFactory::get(Item::GOLD_INGOT, 0, 1 + mt_rand(0, $item->getEnchantmentLevel(Enchantment::FORTUNE)))];
    }
}