<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\object\EnderEyeEntity;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class EnderEyeItem extends Item{

    public function __construct(int $meta = 0){
        parent::__construct(self::ENDER_EYE, $meta, "Ender Eye");
    }

    public function onClickAir(Player $player, Vector3 $directionVector): bool{
        $nbt = Entity::createBaseNBT($player->add(0, $player->getEyeHeight(), 0), $directionVector, $player->yaw, $player->pitch);
        $entity = new EnderEyeEntity($player->getLevel(), $nbt);
        $entity->setOwningEntity($player);
        $entity->setMotion($entity->getMotion()->multiply(1.5));
        $this->pop();
        //TODO End Portal Location
        return true;
    }
}