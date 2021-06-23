<?php

namespace CLADevs\VanillaX\network\protocol;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\InteractPacket;

class InteractPacketX extends InteractPacket{

    public function handle(NetworkSession $handler): bool{
        $handler->handleInteract($this);
        return true; //ignores debug
    }
}