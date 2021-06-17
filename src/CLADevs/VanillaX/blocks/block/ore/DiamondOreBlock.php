<?php

namespace CLADevs\VanillaX\blocks\block\ore;

use pocketmine\block\DiamondOre;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class DiamondOreBlock extends DiamondOre{

    /**
     * @param Item $item
     * @return Item[]
     */
    public function getDropsForCompatibleTool(Item $item): array{
        return [ItemFactory::get(Item::DIAMOND, 0, 1 + mt_rand(0, $item->getEnchantmentLevel(Enchantment::FORTUNE)))];
    }
}