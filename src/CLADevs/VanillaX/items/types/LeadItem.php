<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use CLADevs\VanillaX\entities\utils\interferces\EntityInteractable;
use pocketmine\item\Item;

class LeadItem extends Item implements EntityInteractable{

    public function __construct(int $meta = 0){
        parent::__construct(self::LEAD, $meta, "Lead");
    }

    public function onInteract(EntityInteractResult $result): void{
        //TODO
    }
}