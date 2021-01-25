<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class FishingRodItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::FISHING_ROD, $meta, "Fishing Rod");
    }

    public function onClickAir(Player $player, Vector3 $directionVector): bool{
        //TODO finishing
        return true;
    }
}