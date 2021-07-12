<?php

namespace CLADevs\VanillaX\blocks\block\crops;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Melon as PMMelon;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class Melon extends PMMelon{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::MELON_BLOCK, 0), "Melon Block", new BlockBreakInfo(1.0, BlockToolType::AXE));
    }

    /**
     * @param Item $item
     * @return Item[]
     */
    public function getDropsForCompatibleTool(Item $item): array{
        return [ItemFactory::getInstance()->get(ItemIds::MELON_SLICE, 0, min(9, mt_rand(3, 7) + mt_rand(0, $item->getEnchantmentLevel(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::FORTUNE)))))];
    }
}