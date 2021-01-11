<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;

class RavagerEntity extends Living{

    public $width = 1.9;
    public $height = 1.2;

    const NETWORK_ID = 59; //RAVAGER ID

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(100);
    }

    public function getName(): string{
        return "Ravager";
    }

    protected function sendSpawnPacket(Player $player) : void{
        $pk = new AddActorPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->type ="minecraft:ravager";
        $pk->position = $this->asVector3();
        $pk->motion = $this->getMotion();
        $pk->yaw = $this->yaw;
        $pk->headYaw = $this->yaw; //TODO
        $pk->pitch = $this->pitch;
        $pk->attributes = $this->attributeMap->getAll();
        $pk->metadata = $this->propertyManager->getAll();
        $player->dataPacket($pk);
    }
}