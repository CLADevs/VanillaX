<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class TridentItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::TRIDENT, $meta, "Trident");
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}