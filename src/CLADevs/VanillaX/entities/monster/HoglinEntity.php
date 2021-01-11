<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\traits\EntityAgeable;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;

class HoglinEntity extends LivingEntity{

    public $width = 0.9;
    public $height = 0.9;

    const NETWORK_ID = 124; //HOGLIN ID

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.85, 0.86], [0.9, 0.9]);
        //TODO add Crimson Fungus Item
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