<?php

namespace CLADevs\VanillaX\network\protocol;

use pocketmine\network\mcpe\protocol\PacketHandlerInterface;
use pocketmine\network\mcpe\protocol\PlayerInputPacket;

class PlayerInputPacketX extends PlayerInputPacket{

    public function handle(PacketHandlerInterface $handler): bool{
        parent::handle($handler);
        return true; //ignores debug
    }
}