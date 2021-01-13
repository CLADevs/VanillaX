<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;

class LeadItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::LEAD, $meta, "Lead");
    }
}