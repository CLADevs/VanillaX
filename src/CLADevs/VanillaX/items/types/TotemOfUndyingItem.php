<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class TotemOfUndyingItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::TOTEM, $meta, "Totem of Undying");
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}
