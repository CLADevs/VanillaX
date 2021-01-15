<?php

namespace CLADevs\VanillaX\entities\object;

use CLADevs\VanillaX\entities\utils\EntityInteractable;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\Player;

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

    public function onInteract(Player $player, Item $item): void{
    }
}