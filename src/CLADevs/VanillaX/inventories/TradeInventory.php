<?php

namespace CLADevs\VanillaX\inventories;

use pocketmine\inventory\ContainerInventory;
use pocketmine\network\mcpe\protocol\types\WindowTypes;

class TradeInventory extends ContainerInventory{

    public function getName(): string{
        return "Trade";
    }

    public function getDefaultSize(): int{
        return 3;
    }

    public function getNetworkType(): int{
        return WindowTypes::TRADING;
    }

    public function handlePacket(): void{
    }
}