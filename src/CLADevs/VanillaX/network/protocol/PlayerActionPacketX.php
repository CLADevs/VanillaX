<?php

namespace CLADevs\VanillaX\network\protocol;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;

class PlayerActionPacketX extends PlayerActionPacket{

    public function handle(NetworkSession $handler): bool{
        if($this->action === self::ACTION_SET_ENCHANTMENT_SEED){
            return true; //ignores debug
        }
        return parent::handle($handler);
    }
}