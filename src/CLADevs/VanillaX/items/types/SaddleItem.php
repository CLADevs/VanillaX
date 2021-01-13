<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class SaddleItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::SADDLE, $meta, "Saddle");
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}