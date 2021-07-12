<?php

namespace CLADevs\VanillaX\items\types;

use pocketmine\inventory\ArmorInventory;
use pocketmine\item\Armor;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;

class TurtleHelmetItem extends Armor{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIds::TURTLE_HELMET, 0), "Turtle Helmet", new ArmorTypeInfo(1, 276, ArmorInventory::SLOT_HEAD));
    }
}