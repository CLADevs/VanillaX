<?php

namespace CLADevs\VanillaX\network\protocol;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;

class CommandBlockUpdatePacketX extends CommandBlockUpdatePacket{

    public function handle(NetworkSession $handler): bool{
        parent::handle($handler);
        return true; //ignore debugs
    }
}