<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\item\Shovel;

class NetheriteShovel extends Shovel{

    public function __construct(){
        parent::__construct(ItemIdentifiers::NETHERITE_SHOVEL, 0, "Netherite Shovel", ItemIdentifiers::TIER_NETHERITE);
    }

    public function getAttackPoints(): int{
        return 6; //Netherite Shovel Damage
    }

    public function getMaxDurability(): int{
        return 2032;
    }

    protected function getBaseMiningEfficiency(): float{
        return 9;
    }
}