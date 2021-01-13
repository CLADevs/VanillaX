<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class LingeringPotionItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::LINGERING_POTION, $meta, "Lingering Potion");
    }
}