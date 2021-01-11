<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;

class StriderEntity extends Living{

    public $width = 0.9;
    public $height = 1.7;

    const NETWORK_ID = 125; //STRIDER ID

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Strider";
    }

    protected function sendSpawnPacket(Player $player) : void{
        $pk = new AddActorPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->type ="minecraft:strider";
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