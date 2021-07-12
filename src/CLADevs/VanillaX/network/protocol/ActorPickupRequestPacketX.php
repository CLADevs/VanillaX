<?php

namespace CLADevs\VanillaX\network\protocol;

use pocketmine\network\mcpe\protocol\ActorPickRequestPacket;
use pocketmine\network\mcpe\protocol\PacketHandlerInterface;

class ActorPickupRequestPacketX extends ActorPickRequestPacket{

    public function handle(PacketHandlerInterface $handler): bool{
        $handler->handleActorPickRequest($this);
        return true; //ignores debug
    }
}