<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;

class HoglinEntity extends Living{

    public $width = 0.85;
    public $height = 0.85;

    const NETWORK_ID = 124; //HOGLIN ID

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(40);
    }

    public function getName(): string{
        return "Hoglin";
    }

    protected function sendSpawnPacket(Player $player) : void{
        $pk = new AddActorPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->type ="minecraft:hoglin";
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