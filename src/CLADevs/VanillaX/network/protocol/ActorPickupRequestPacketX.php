<?php

namespace CLADevs\VanillaX\network\protocol;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ActorPickRequestPacket;

class ActorPickupRequestPacketX extends ActorPickRequestPacket{

    public function handle(NetworkSession $handler): bool{
        $handler->handleActorPickRequest($this);
        return true; //ignores debug
    }
}