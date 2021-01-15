<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\item\Armor;

class NetheriteLeggingsItem extends Armor{

    public function __construct(int $meta = 0){
        parent::__construct(ItemIdentifiers::NETHERITE_LEGGINGS, $meta, "Netherite Leggings");
    }

    public function getDefensePoints(): int{
        return 6;
    }

    public function getMaxDurability(): int{
        return 556;
    }
}
