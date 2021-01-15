<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\utils\EntityInteractable;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\Player;

class LeadItem extends Item implements EntityInteractable{

    public function __construct(int $meta = 0){
        parent::__construct(self::LEAD, $meta, "Lead");
    }

    public function onInteractWithEntity(Player $player, Entity $entity): void{
        //TODO
    }
}