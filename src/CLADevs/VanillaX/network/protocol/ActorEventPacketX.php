<?php

namespace CLADevs\VanillaX\network\protocol;

use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\PacketHandlerInterface;

class ActorEventPacketX extends ActorEventPacket{

    public function handle(PacketHandlerInterface $handler): bool{
        $parent = parent::handle($handler);

        if($this->event === self::PLAYER_ADD_XP_LEVELS || $this->event === self::COMPLETE_TRADE){
            return true; //ignores debug
        }
        return $parent;
    }
}