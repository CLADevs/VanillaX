<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class UndyedShulkerBoxItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::UNDYED_SHULKER_BOX, $meta, "Shulker Box");
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}