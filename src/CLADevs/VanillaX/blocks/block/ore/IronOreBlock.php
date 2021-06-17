<?php

namespace CLADevs\VanillaX\blocks\block\ore;

use pocketmine\block\IronOre;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class IronOreBlock extends IronOre{

    /**
     * @param Item $item
     * @return Item[]
     */
    public function getDropsForCompatibleTool(Item $item): array{
        return [ItemFactory::get(Item::IRON_ORE, 0, 1 + mt_rand(0, $item->getEnchantmentLevel(Enchantment::FORTUNE)))];
    }
}