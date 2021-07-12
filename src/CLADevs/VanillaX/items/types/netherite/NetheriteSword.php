<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\Sword;
use pocketmine\item\ToolTier;

class NetheriteSword extends Sword{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIdentifiers::NETHERITE_SWORD, 0), "Netherite Sword", ToolTier::DIAMOND());
    }

    public function getAttackPoints() : int{
        return 9; //9 damage for Netherite Sword
    }

    public function getMaxDurability(): int{
        return 2032;
    }
}