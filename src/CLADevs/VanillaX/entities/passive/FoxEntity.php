<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\traits\EntityAgeable;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;

class FoxEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 0.7;

    const NETWORK_ID = 121; //FOX ID

    protected function initEntity(): void{
        parent::initEntity();
        //TODO find baby fox width, width
        $this->ageable = new EntityAgeable($this, [0.6, 0.7], [0.6, 0.7]);
        $this->ageable->setGrowthItems([ItemIds::SWEET_BERRIES]);
    }

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