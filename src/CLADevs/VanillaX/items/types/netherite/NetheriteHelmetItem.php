<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\item\Armor;

class NetheriteHelmetItem extends Armor{

    public function __construct(int $meta = 0){
        parent::__construct(ItemIdentifiers::NETHERITE_HELMET, $meta, "Netherite Helmet");
    }

    public function getDefensePoints(): int{
        return 3;
    }

    public function getMaxDurability(): int{
        return 408;
    }
}
