<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class PaintingItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::PAINTING, $meta, "Painting");
    }
}