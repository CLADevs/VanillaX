<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use CLADevs\VanillaX\entities\utils\interferces\EntityInteractable;
use pocketmine\item\Item;

class NameTagItem extends Item implements EntityInteractable{

    public function __construct(int $meta = 0){
        parent::__construct(self::NAMETAG, $meta, "Name Tag");
    }

    public function onInteract(EntityInteractResult $result): void{
        $player = $result->getPlayer();
        $entity = $result->getEntity();

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