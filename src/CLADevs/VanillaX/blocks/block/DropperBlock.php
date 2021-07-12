<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\DropperTile;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Opaque;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class DropperBlock extends Opaque{

    //TODO tile
    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::DROPPER, 0), "Dropper", new BlockBreakInfo(3.5));
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

            if($tile instanceof DropperTile){
                $player->setCurrentWindow($tile->getInventory());
            }
        }
        return true;
    }
}