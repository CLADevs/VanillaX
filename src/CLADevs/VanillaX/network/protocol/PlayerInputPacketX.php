<?php

namespace CLADevs\VanillaX\network\protocol;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\PlayerInputPacket;

class PlayerInputPacketX extends PlayerInputPacket{

    public function handle(NetworkSession $handler): bool{
        parent::handle($handler);
        return true; //ignores debug
    }
}