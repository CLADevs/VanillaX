<?php

namespace CLADevs\VanillaX\network\protocol;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\AnvilDamagePacket;

class AnvilDamagePacketX extends AnvilDamagePacket{

    public function handle(NetworkSession $handler): bool{
        $handler->handleAnvilDamage($this);
        return true; //ignores debug
    }
}