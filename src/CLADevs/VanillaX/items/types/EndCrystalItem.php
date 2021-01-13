<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class EndCrystalItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::END_CRYSTAL, $meta, "End Crystal");
    }
}