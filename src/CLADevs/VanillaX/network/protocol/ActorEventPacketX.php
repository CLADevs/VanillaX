<?php

namespace CLADevs\VanillaX\network\protocol;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ActorEventPacket;

class ActorEventPacketX extends ActorEventPacket{

    public function handle(NetworkSession $handler): bool{
        $parent = parent::handle($handler);

        if($this->event === self::PLAYER_ADD_XP_LEVELS){
            return true; //ignores debug
        }
        return $parent;
    }
}