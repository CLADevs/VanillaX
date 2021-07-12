<?php

namespace CLADevs\VanillaX\network\protocol;

use pocketmine\network\mcpe\protocol\FilterTextPacket;
use pocketmine\network\mcpe\protocol\PacketHandlerInterface;

class FilterTextPacketX extends FilterTextPacket{

    public function handle(PacketHandlerInterface $handler): bool{
        //This is called once Anvil item name is changing
        $handler->handleFilterText($this);
        return true; //ignores debug
    }
}