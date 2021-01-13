<?php

namespace CLADevs\VanillaX\inventories;

use pocketmine\inventory\ContainerInventory;
use pocketmine\network\mcpe\protocol\types\WindowTypes;

class HopperInventory extends ContainerInventory{

    public function getName(): string{
        return "Hopper";
    }

    public function getNetworkType(): int{
        return WindowTypes::HOPPER;
    }

    public function getDefaultSize(): int{
        return 5;
    }
}
