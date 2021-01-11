<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\traits\EntityAgeable;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;

class StriderEntity extends LivingEntity{

    public $width = 0.9;
    public $height = 1.7;

    const NETWORK_ID = 125; //STRIDER ID

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.45, 0.85], [0.9, 1.7]);
        //TODO item Warped fungus
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