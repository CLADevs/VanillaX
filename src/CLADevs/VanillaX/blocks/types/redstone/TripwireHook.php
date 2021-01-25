<?php

namespace CLADevs\VanillaX\blocks\types\redstone;

use pocketmine\block\Block;
use pocketmine\block\TripwireHook as PMTripwireHook;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class TripwireHook extends PMTripwireHook{

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        $faces = [
            Vector3::SIDE_SOUTH => 0,
            Vector3::SIDE_WEST => 1,
            Vector3::SIDE_NORTH => 2,
            Vector3::SIDE_EAST => 3
        ];
        if(($meta = $faces[$face] ?? null) === null){
            return false;
        }
        $this->meta = $meta;
        return parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }
}