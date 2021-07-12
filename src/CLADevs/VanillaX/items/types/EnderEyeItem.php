<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class EnderEyeItem extends Item{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIds::ENDER_EYE, 0), "Ender Eye");
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult{
        //        $nbt = Entity::createBaseNBT($player->add(0, $player->getEyeHeight(), 0), $directionVector, $player->yaw, $player->pitch);
        //        $entity = new EnderEyeEntity($player->getLevel(), $nbt);
        //        $entity->setOwningEntity($player);
        //        $entity->setMotion($entity->getMotion()->multiply(1.5));
        //        $this->pop();
        //        //TODO End Portal Location
        //        return true;
        return parent::onClickAir($player, $directionVector);
    }
}