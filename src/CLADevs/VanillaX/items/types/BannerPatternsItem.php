<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class BannerPatternsItem extends Item{

    const TYPE_CREEPER_CHARGE = 0;
    const TYPE_SKULL_CHARGE = 1;
    const TYPE_FLOWER_CHARGE = 2;
    const TYPE_THING = 3;
    const TYPE_FIELD_MASONED = 4;
    const TYPE_BORDURE_INDENTED = 5;
    const TYPE_SNOUT = 6;

    public function __construct(int $meta = 0){
        parent::__construct(self::BANNER_PATTERN, $meta, "Banner Pattern");
    }
}