<?php

namespace CLADevs\VanillaX\blocks\block\redstone;

use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;

class RedstoneWire extends Flowable{

    public function __construct(int $meta = 0){
        parent::__construct(self::REDSTONE_WIRE, $meta, "Redstone Dust", ItemIds::REDSTONE_DUST);
    }

    public function getHardness(): float{
        return 0;
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        if($blockClicked->getId() === self::REDSTONE_WIRE){
            return false;
        }
        return parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }
}