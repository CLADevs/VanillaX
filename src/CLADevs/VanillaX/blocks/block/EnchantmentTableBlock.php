<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\inventories\types\EnchantInventory;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\tile\EnchantTable;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class EnchantmentTableBlock extends Transparent{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::ENCHANTING_TABLE, 0, null, EnchantTable::class), "Enchanting Table", new BlockBreakInfo(5, BlockToolType::PICKAXE, 0, 1200));
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        if($player !== null){
            $player->setCurrentWindow(new EnchantInventory($this->getPos()));
        }
        return true;
    }
}