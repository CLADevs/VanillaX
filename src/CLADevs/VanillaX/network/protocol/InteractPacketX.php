<?php

namespace CLADevs\VanillaX\network\protocol;

use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\PacketHandlerInterface;

class InteractPacketX extends InteractPacket{

    public function handle(PacketHandlerInterface $handler): bool{
        $handler->handleInteract($this);
        return true; //ignores debug
    }
}