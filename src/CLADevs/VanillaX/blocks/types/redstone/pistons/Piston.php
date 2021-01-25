<?php

namespace CLADevs\VanillaX\blocks\types\redstone\pistons;

use pocketmine\block\Block;
use pocketmine\block\Solid;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Piston extends Solid{

    public function __construct(int $meta = 0){
        parent::__construct(self::PISTON, $meta, "Piston");
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        $faces = [
            Vector3::SIDE_DOWN => 0,
            Vector3::SIDE_UP => 1,
            Vector3::SIDE_NORTH => 2,
            Vector3::SIDE_SOUTH => 3,
            Vector3::SIDE_WEST => 5,
            Vector3::SIDE_EAST => 4
        ];
        if(($meta = $faces[$face] ?? null) === null){
            return false;
        }
        $this->meta = $meta;
        return parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    public function getHardness(): float{
        return 1.5;
    }

    public function getBlastResistance(): float{
        return 0.5;
    }
}