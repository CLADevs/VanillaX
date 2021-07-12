<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use CLADevs\VanillaX\entities\utils\interfaces\EntityInteractable;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;

class LeadItem extends Item implements EntityInteractable{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIds::LEAD, 0), "Lead");
    }

    public function onInteract(EntityInteractResult $result): void{
        //TODO
    }
}