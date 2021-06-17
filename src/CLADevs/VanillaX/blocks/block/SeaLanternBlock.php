<?php

namespace CLADevs\VanillaX\blocks\block;

use pocketmine\block\SeaLantern;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class SeaLanternBlock extends SeaLantern{

    /**
     * @param Item $item
     * @return Item[]
     */
    public function getDropsForCompatibleTool(Item $item): array{
        return [ItemFactory::get(Item::PRISMARINE_CRYSTALS, 0, min(5, mt_rand(2, 3) + $item->getEnchantment(Enchantment::FORTUNE)->getLevel()))];
    }
}
