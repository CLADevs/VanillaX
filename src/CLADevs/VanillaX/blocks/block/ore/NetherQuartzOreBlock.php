<?php

namespace CLADevs\VanillaX\blocks\block\ore;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\NetherQuartzOre;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\ToolTier;

class NetherQuartzOreBlock extends NetherQuartzOre{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::NETHER_QUARTZ_ORE, 0), "Nether Quartz Ore", new BlockBreakInfo(3.0, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel()));
    }

    /**
     * @param Item $item
     * @return Item[]
     */
    public function getDropsForCompatibleTool(Item $item): array{
        return [ItemFactory::getInstance()->get(ItemIds::NETHER_QUARTZ, 0, 1 + mt_rand(0, $item->getEnchantmentLevel(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::FORTUNE))))];
    }
}