<?php

namespace CLADevs\VanillaX\entities\monster;

use pocketmine\entity\Living;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;

class PillagerEntity extends Living{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = 114; //PILLAGER ID

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(24);
    }

    public function getName(): string{
        return "Pillager";
    }

    protected function sendSpawnPacket(Player $player) : void{
        $pk = new AddActorPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->type ="minecraft:pillager";
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