<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\item\Axe;

class NetheriteAxe extends Axe{

    public function __construct(){
        parent::__construct(ItemIdentifiers::NETHERITE_AXE, 0, "Netherite Axe", ItemIdentifiers::TIER_NETHERITE);
    }

    public function getAttackPoints(): int{
        return 8; //Netherite Axe Damage
    }

    public function getMaxDurability(): int{
        return 2031;
    }

    protected function getBaseMiningEfficiency(): float{
        return 9;
    }
}