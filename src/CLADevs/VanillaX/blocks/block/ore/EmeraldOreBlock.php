<?php

namespace CLADevs\VanillaX\blocks\block\ore;

use pocketmine\block\EmeraldOre;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class EmeraldOreBlock extends EmeraldOre{

    /**
     * @param Item $item
     * @return Item[]
     */
    public function getDropsForCompatibleTool(Item $item): array{
        return [ItemFactory::get(Item::EMERALD, 0, 1 + mt_rand(0, $item->getEnchantmentLevel(Enchantment::FORTUNE)))];
    }
}