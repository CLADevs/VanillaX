<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\traits\EntityInteractable;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\Player;

class NameTagItem extends Item implements EntityInteractable{

    public function __construct(int $meta = 0){
        parent::__construct(self::NAMETAG, $meta, "Name Tag");
    }

    public function onInteractWithEntity(Player $player, Entity $entity): void{
        if($this->getName() === $this->getVanillaName() || $entity->getNameTag() === $this->getName()){
            return;
        }
        $entity->setNameTag($this->getName());
        if($player->isSurvival() || $player->isAdventure()){
            $this->pop();
            $player->getInventory()->setItemInHand($this);
        }
    }
}