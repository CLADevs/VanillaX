<?php

namespace CLADevs\VanillaX\blocks\block\ore;

use pocketmine\block\LapisOre;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class LapisOreBlock extends LapisOre{

    /**
     * @param Item $item
     * @return Item[]
     */
    public function getDropsForCompatibleTool(Item $item): array{
        $fortune = $item->getEnchantmentLevel(Enchantment::FORTUNE);
        return [ItemFactory::get(Item::DYE, 4, $fortune >= 1 ? mt_rand(4, 9 * ($fortune + 1)) : mt_rand(4, 9))];
    }
}