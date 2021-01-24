<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class ShieldItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::SHIELD, $meta, "Shield");
    }
}