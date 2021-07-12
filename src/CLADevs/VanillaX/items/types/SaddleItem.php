<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\entity\Entity;
use pocketmine\entity\EntityBlockLegacyIds;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class SaddleItem extends Item{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIds::SADDLE, 0), "Saddle");
    }

    public function onInteractWithEntity(Player $player, Entity $entity): void{
//        if(in_array($entity->getId(), [EntityBlockLegacyBlockLegacyIds::HORSE, EntityBlockLegacyBlockLegacyIds::PIG, EntityBlockLegacyBlockLegacyIds::DONKEY, EntityBlockLegacyBlockLegacyIds::MULE])){
//            //TODO
//        }
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}