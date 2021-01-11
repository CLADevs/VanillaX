<?php

namespace CLADevs\VanillaX\entities\passive;

use pocketmine\entity\Living;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;

class FoxEntity extends Living{

    public $width = 0.6;
    public $height = 0.7;

    const NETWORK_ID = 121; //FOX ID

    public function getName(): string{
        return "Fox";
    }

    protected function sendSpawnPacket(Player $player) : void{
        $pk = new AddActorPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->type ="minecraft:fox";
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