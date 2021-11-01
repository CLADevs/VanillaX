<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\Armor;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ItemIdentifier;

class NetheriteChestplate extends Armor{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIdentifiers::NETHERITE_CHESTPLATE, 0), "Netherite Chestplate", new ArmorTypeInfo(5, 593, ArmorInventory::SLOT_CHEST));
    }
}
