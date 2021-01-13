<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class MinecartItem extends Item{

    public function getMaxStackSize(): int{
        return 1;
    }
}