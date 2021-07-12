<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;

class CauldronItem extends Item{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIds::CAULDRON, 0), "Cauldron");
    }

    public function getBlock(?int $clickedFace = null): Block{
        return BlockFactory::getInstance()->get(BlockLegacyIds::CAULDRON_BLOCK, 0);
    }
}