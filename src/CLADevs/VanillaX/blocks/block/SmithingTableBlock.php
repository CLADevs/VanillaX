<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\inventories\types\SmithingTableInventory;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class SmithingTableBlock extends Transparent{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::SMITHING_TABLE, 0, ItemIdentifiers::SMITHING_TABLE), "Smithing Table", new BlockBreakInfo(2.5, BlockToolType::AXE, 0, 2.5));
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        $player?->setCurrentWindow(new SmithingTableInventory($this->getPosition()));
        return true;
    }
}