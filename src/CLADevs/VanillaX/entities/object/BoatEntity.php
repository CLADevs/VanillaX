<?php

namespace CLADevs\VanillaX\entities\object;

use CLADevs\VanillaX\entities\utils\EntityInteractable;
use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use pocketmine\entity\Entity;

class BoatEntity extends Entity implements EntityInteractable{

    public $width = 1.4;
    public $height = 0.455;
    protected $gravity = 0.05;

    const NETWORK_ID = self::BOAT;

    public function onInteract(EntityInteractResult $result): void{
        //TODO
    }
}