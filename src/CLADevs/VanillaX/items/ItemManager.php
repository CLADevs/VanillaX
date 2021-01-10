<?php

namespace CLADevs\VanillaX\items;

use CLADevs\VanillaX\items\types\ElytraItem;
use pocketmine\item\ItemFactory;

class ItemManager{

    public function startup(): void{
        ItemFactory::registerItem(new ElytraItem(), true);
    }
}