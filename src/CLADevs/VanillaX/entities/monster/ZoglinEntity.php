<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\LivingEntity;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;

class ZoglinEntity extends LivingEntity{

    public $width = 0.9;
    public $height = 0.9;

    const NETWORK_ID = 126; //ZOGLIN ID

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(40);
    }

    public function getName(): string{
        return "Zoglin";
    }

    protected function sendSpawnPacket(Player $player) : void{
        $pk = new AddActorPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->type ="minecraft:zoglin";
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