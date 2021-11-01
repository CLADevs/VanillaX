<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\Armor;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ItemIdentifier;

class NetheriteLeggings extends Armor{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIdentifiers::NETHERITE_LEGGINGS, 0), "Netherite Leggings", new ArmorTypeInfo(6, 556, ArmorInventory::SLOT_LEGS));
    }
}
