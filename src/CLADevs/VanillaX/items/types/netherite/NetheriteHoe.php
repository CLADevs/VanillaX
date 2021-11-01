<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\item\Hoe;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ToolTier;

class NetheriteHoe extends Hoe{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIdentifiers::NETHERITE_HOE, 0), "Netherite Hoe", ToolTier::DIAMOND());
    }

    public function getMaxDurability(): int{
        return 2032;
    }

    protected function getBaseMiningEfficiency() : float{
        return 12; //Netherite Hoe Speed
    }
}