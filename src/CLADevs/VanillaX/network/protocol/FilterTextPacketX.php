<?php

namespace CLADevs\VanillaX\network\protocol;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\FilterTextPacket;

class FilterTextPacketX extends FilterTextPacket{

    public function handle(NetworkSession $handler): bool{
        //This is called once Anvil item name is changing
        $handler->handleFilterText($this);
        return true; //ignores debug
    }
}