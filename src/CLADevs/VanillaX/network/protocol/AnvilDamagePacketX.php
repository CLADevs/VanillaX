<?php

namespace CLADevs\VanillaX\network\protocol;

use pocketmine\network\mcpe\protocol\AnvilDamagePacket;
use pocketmine\network\mcpe\protocol\PacketHandlerInterface;

class AnvilDamagePacketX extends AnvilDamagePacket{

    public function handle(PacketHandlerInterface $handler): bool{
        $handler->handleAnvilDamage($this);
        return true; //ignores debug
    }
}