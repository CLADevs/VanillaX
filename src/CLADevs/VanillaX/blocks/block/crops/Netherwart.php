<?php

namespace CLADevs\VanillaX\blocks\block\crops;

use pocketmine\block\NetherWartPlant;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class Netherwart extends NetherWartPlant{

    /**
     * @param Item $item
     * @return Item[]
     */
    public function getDropsForCompatibleTool(Item $item): array{
        return [ItemFactory::get($this->getItemId(), 0, min(7, ($this->getDamage() === 3 ? mt_rand(2, 4) + $item->getEnchantmentLevel(Enchantment::FORTUNE) : 1)))];
    }
}