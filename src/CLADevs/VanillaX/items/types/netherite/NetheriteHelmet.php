<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\Armor;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ItemIdentifier;

class NetheriteHelmet extends Armor{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIdentifiers::NETHERITE_HELMET, 0), "Netherite Helmet", new ArmorTypeInfo(3, 408, ArmorInventory::SLOT_HEAD));
    }
}
