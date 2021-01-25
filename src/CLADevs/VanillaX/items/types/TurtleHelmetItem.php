<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Armor;

class TurtleHelmetItem extends Armor{

    public function __construct(int $meta = 0){
        parent::__construct(self::TURTLE_HELMET, $meta, "Turtle Helmet");
    }

    public function getMaxStackSize(): int{
        return 1;
    }

    public function getMaxDurability(): int{
        return 276;
    }
}