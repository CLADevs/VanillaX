<?php

namespace CLADevs\VanillaX\blocks\types\chorus;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class ChorusPlant extends Transparent{

    public function __construct(int $meta = 0){
        parent::__construct(self::CHORUS_PLANT, $meta, "Chorus Plant");
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        if($blockClicked->getId() !== BlockIds::END_STONE){
            return false;
        }
        return parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    public function getHardness(): float{
        return 0.4;
    }
}