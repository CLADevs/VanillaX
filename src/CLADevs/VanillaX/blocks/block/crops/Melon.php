<?php

namespace CLADevs\VanillaX\blocks\block\crops;

use pocketmine\block\Melon as PMMelon;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class Melon extends PMMelon{

    /**
     * @param Item $item
     * @return Item[]
     */
    public function getDropsForCompatibleTool(Item $item): array{
        return [ItemFactory::get(Item::MELON_SLICE, 0, min(9, mt_rand(3, 7) + mt_rand(0, $item->getEnchantmentLevel(Enchantment::FORTUNE))))];
    }
}