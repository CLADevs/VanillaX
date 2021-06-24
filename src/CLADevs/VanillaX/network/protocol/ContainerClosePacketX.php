<?php

namespace CLADevs\VanillaX\network\protocol;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;

class ContainerClosePacketX extends ContainerClosePacket{

    public function handle(NetworkSession $handler): bool{
        $handler->handleContainerClose($this);
        return $this->windowId === 255; //ignores for trade ui
    }
}