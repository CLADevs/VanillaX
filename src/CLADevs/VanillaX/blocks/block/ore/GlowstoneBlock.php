<?php

namespace CLADevs\VanillaX\blocks\block\ore;

use pocketmine\block\Glowstone;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class GlowstoneBlock extends Glowstone{

    /**
     * @param Item $item
     * @return Item[]
     */
    public function getDropsForCompatibleTool(Item $item): array{
        return [ItemFactory::get(Item::GLOWSTONE_DUST, 0, min(4, mt_rand(2, 4) + mt_rand(0, $item->getEnchantmentLevel(Enchantment::FORTUNE))))];
    }
}