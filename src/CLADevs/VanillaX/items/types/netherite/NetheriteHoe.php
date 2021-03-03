<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\item\Hoe;

class NetheriteHoe extends Hoe{

    public function __construct(){
        parent::__construct(ItemIdentifiers::NETHERITE_HOE, 0, "Netherite Hoe", ItemIdentifiers::TIER_NETHERITE);
    }

    public function getMaxDurability(): int{
        return 2032;
    }

    protected function getBaseMiningEfficiency() : float{
        return 12; //Netherite Hoe Speed
    }
}