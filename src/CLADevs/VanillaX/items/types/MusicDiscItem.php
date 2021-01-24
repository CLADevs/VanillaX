<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\items\utils\NonAutomaticCallItemTrait;
use pocketmine\item\Item;

class MusicDiscItem extends Item implements NonAutomaticCallItemTrait{

    public function getMaxStackSize(): int{
        return 1;
    }
}