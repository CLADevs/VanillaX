<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\HopperTile;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class HopperBlock extends Transparent{
    //TODO tile

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::HOPPER_BLOCK, 0), "Hopper", new BlockBreakInfo(3, BlockToolType::PICKAXE, 0, 4.8));
    }

    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null): bool{
//        $faces = [
//            Facing::DOWN => 0,
//            Facing::UP => 1,
//            Facing::NORTH => 2,
//            Facing::SOUTH => 3,
//            Facing::WEST => 4,
//            Facing::EAST => 5
//        ];
//        $this->facing = $faces[$face] ?? $face;
        //TODO facing
        return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        if($player !== null){
            $tile = $this->getPos()->getWorld()->getTile($this->getPos());

            if($tile instanceof HopperTile){
                $player->setCurrentWindow($tile->getInventory());
            }
        }
        return true;
    }
}