<?php

namespace CLADevs\VanillaX\entities\object;

use CLADevs\VanillaX\entities\utils\EntityInteractable;
use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use pocketmine\entity\Entity;

class ArmorStandEntity extends Entity implements EntityInteractable{

    public $width = 0.5;
    public $height = 1.975;
    protected $gravity = 0.5;

    const NETWORK_ID = self::ARMOR_STAND;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(6);
        $this->setHealth(6);
    }

    public function onInteract(EntityInteractResult $result): void{
        //TODO
    }
}