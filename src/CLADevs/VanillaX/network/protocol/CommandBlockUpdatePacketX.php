<?php

namespace CLADevs\VanillaX\network\protocol;

use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\network\mcpe\protocol\PacketHandlerInterface;

class CommandBlockUpdatePacketX extends CommandBlockUpdatePacket{

    public function handle(PacketHandlerInterface $handler): bool{
        parent::handle($handler);
        return true; //ignore debugs
    }
}