<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class NameTagItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::NAMETAG, $meta, "Name Tag");
    }
}