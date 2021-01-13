<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class EnderEyeItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::ENDER_EYE, $meta, "Ender Eye");
    }
}