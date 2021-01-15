<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\item\Armor;

class NetheriteChestplateItem extends Armor{

    public function __construct(int $meta = 0){
        parent::__construct(ItemIdentifiers::NETHERITE_CHESTPLATE, $meta, "Netherite Chestplate");
    }

    public function getDefensePoints(): int{
        return 8;
    }

    public function getMaxDurability(): int{
        return 593;
    }
}
