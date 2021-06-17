<?php

namespace CLADevs\VanillaX\blocks\block\ore;

use pocketmine\block\RedstoneOre;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class RedstoneOreBlock extends RedstoneOre{

    /**
     * @param Item $item
     * @return Item[]
     */
    public function getDropsForCompatibleTool(Item $item): array{
        return [ItemFactory::get(Item::REDSTONE_DUST, 0, min(8, mt_rand(4, 5) + $item->getEnchantmentLevel(Enchantment::FORTUNE)))];
    }
}
