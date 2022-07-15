<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\inventories\types\SmithingInventory;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\item\ToolTier;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class SmithingTableBlock extends Transparent{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::SMITHING_TABLE, 0), "Smithing Table", new BlockBreakInfo(5.0, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel(), 6000.0));
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        $player?->setCurrentWindow(new SmithingInventory($this->getPosition()));
        return true;
    }
}