<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\Shovel;
use pocketmine\item\ToolTier;

class NetheriteShovel extends Shovel{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIdentifiers::NETHERITE_SHOVEL, 0), "Netherite Shovel", ToolTier::DIAMOND());
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