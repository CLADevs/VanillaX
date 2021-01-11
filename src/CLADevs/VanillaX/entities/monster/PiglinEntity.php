<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\traits\EntityAgeable;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;

class PiglinEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = 123; //PIGLIN ID

    protected function initEntity(): void{
        parent::initEntity();
        //TODO
        $this->ageable = new EntityAgeable($this, [0.6, 1.9], [0.6, 1.9]);
        $this->ageable->setCanBeBredByPlayer(false);
        $this->setMaxHealth(16);
    }

    public function getName(): string{
        return "Piglin";
    }

    protected function sendSpawnPacket(Player $player) : void{
        $pk = new AddActorPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->type ="minecraft:piglin";
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