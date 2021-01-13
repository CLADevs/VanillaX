<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class FireworkRocketItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::FIREWORKS, $meta, "Firework Rocket");
    }
}