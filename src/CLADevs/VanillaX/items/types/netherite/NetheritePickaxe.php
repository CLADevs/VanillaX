<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\item\Pickaxe;

class NetheritePickaxe extends Pickaxe{

    public function __construct(){
        parent::__construct(ItemIdentifiers::NETHERITE_PICKAXE, 0, "Netherite Pickaxe", ItemIdentifiers::TIER_NETHERITE);
    }

    public function getAttackPoints(): int{
        return 7; //Netherite Pickaxe Damage
    }

    public function getMaxDurability(): int{
        return 2032;
    }

    protected function getBaseMiningEfficiency(): float{
        return 10;
    }
}