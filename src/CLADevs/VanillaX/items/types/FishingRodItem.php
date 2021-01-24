<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class FishingRodItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::FISHING_ROD, $meta, "Fishing Rod");
    }
}