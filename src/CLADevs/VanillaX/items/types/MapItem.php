<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\items\utils\NonAutomaticCallItemTrait;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;

class MapItem extends Item implements NonAutomaticCallItemTrait{

    const MAP_EMPTY = ItemIds::EMPTY_MAP;
    const MAP_FILLED = ItemIds::FILLED_MAP;

    public function __construct(int $id){
        parent::__construct($id, 0, $id === self::MAP_EMPTY ? "Empty Map" : "Map");
    }
}