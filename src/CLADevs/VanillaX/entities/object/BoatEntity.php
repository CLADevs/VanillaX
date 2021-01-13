<?php

namespace CLADevs\VanillaX\entities\object;

use CLADevs\VanillaX\entities\traits\EntityInteractable;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\Player;

class BoatEntity extends Entity implements EntityInteractable{

    public $width = 1.4;
    public $height = 0.455;
    protected $gravity = 0.05;

    const NETWORK_ID = self::BOAT;

    public function onInteract(Player $player, Item $item): void{
    }
}