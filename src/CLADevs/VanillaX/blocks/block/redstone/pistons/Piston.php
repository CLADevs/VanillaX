<?php

namespace CLADevs\VanillaX\blocks\block\redstone\pistons;

use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class Piston extends Opaque implements NonAutomaticCallItemTrait{

    public function __construct(BlockIdentifier $id, string $name){
        parent::__construct($id, $name, new BlockBreakInfo(1.5, BlockToolType::PICKAXE, 0, 0.5));
    }

    public function place(BlockTransaction $tr, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
//        $faces = [
//            Facing::DOWN => 0,
//            Facing::UP => 1,
//            Facing::NORTH => 2,
//            Facing::SOUTH => 3,
//            Facing::WEST => 5,
//            Facing::EAST => 4
//        ];
//        if(($facing = $faces[$face] ?? null) === null){
//            return false;
//        }
//        $this->facing = $facing;
        //TODO facing
        return parent::place($tr, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }
}