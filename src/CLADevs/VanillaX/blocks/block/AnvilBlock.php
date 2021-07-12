<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\inventories\types\AnvilInventory;
use pocketmine\block\Anvil;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\item\Item;
use pocketmine\item\ToolTier;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class AnvilBlock extends Anvil{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::ANVIL, 0), "Anvil", new BlockBreakInfo(5.0, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel(), 6000.0));
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        if($player instanceof Player){
            $player->setCurrentWindow(new AnvilInventory($this->pos));
        }
        return true;
    }
}