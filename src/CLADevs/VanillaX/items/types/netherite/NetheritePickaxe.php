<?php

namespace CLADevs\VanillaX\items\types\netherite;

use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\Pickaxe;
use pocketmine\item\ToolTier;

class NetheritePickaxe extends Pickaxe{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIdentifiers::NETHERITE_PICKAXE, 0), "Netherite Pickaxe", ToolTier::DIAMOND());
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