<?php

namespace CLADevs\VanillaX\blocks\block\chorus;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class ChorusFlower extends Transparent{

    public function __construct(int $meta = 0){
        parent::__construct(self::CHORUS_FLOWER, $meta, "Chorus Flower");
    }

    public function getHardness(): float{
        return 0.4;
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        if($blockClicked->getId() !== BlockIds::END_STONE && $blockClicked->getId() !== BlockIds::CHORUS_PLANT){
            return false;
        }
        return parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }
}