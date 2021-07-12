<?php

namespace CLADevs\VanillaX\network\protocol;

use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\PacketHandlerInterface;

class ContainerClosePacketX extends ContainerClosePacket{

    public function handle(PacketHandlerInterface $handler): bool{
        $handler->handleContainerClose($this);
        return $this->windowId === 255; //ignores for trade ui
    }
}