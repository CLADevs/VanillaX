<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\entity\Entity;
use pocketmine\entity\EntityIds;
use pocketmine\item\Item;
use pocketmine\Player;

class SaddleItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::SADDLE, $meta, "Saddle");
    }

    public function onInteractWithEntity(Player $player, Entity $entity): void{
//        if(in_array($entity->getId(), [EntityIds::HORSE, EntityIds::PIG, EntityIds::DONKEY, EntityIds::MULE])){
//            //TODO
//        }
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}