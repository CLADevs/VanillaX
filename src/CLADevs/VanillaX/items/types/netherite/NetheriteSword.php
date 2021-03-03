<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\item\Sword;

class NetheriteSword extends Sword{

    public function __construct(){
        parent::__construct(ItemIdentifiers::NETHERITE_SWORD, 0, "Netherite Sword", ItemIdentifiers::TIER_NETHERITE);
    }

    public function getAttackPoints() : int{
        return 9; //9 damage for Netherite Sword
    }

    public function getMaxDurability(): int{
        return 2032;
    }
}