<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class FireworkStarItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::FIREWORKS_CHARGE, $meta, "Firework Star");
    }
}