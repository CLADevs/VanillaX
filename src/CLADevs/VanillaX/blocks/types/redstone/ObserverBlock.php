<?php

namespace CLADevs\VanillaX\blocks\types\redstone;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class ObserverBlock extends Transparent{

    public function __construct(int $meta = 0){
        parent::__construct(BlockIds::OBSERVER, $meta);
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        $faces = [
            Vector3::SIDE_DOWN => 0,
            Vector3::SIDE_UP => 1,
            Vector3::SIDE_NORTH => 2,
            Vector3::SIDE_SOUTH => 3,
            Vector3::SIDE_WEST => 4,
            Vector3::SIDE_EAST => 5
        ];
        if(($meta = $faces[$face] ?? null) === null){
            return false;
        }
        $this->meta = $meta;
        return parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    public function getHardness(): float{
        return 3.5;
    }
}