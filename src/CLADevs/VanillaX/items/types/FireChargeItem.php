<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class FireChargeItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::FIRE_CHARGE, $meta, "Fire Charge");
    }
}