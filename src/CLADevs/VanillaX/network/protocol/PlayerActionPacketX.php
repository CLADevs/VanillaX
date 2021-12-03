<?php

namespace CLADevs\VanillaX\network\protocol;

use pocketmine\network\mcpe\protocol\PacketHandlerInterface;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\types\PlayerAction;

class PlayerActionPacketX extends PlayerActionPacket{

    public function handle(PacketHandlerInterface $handler): bool{
        if($this->action === PlayerAction::SET_ENCHANTMENT_SEED){
            return true; //ignores debug
        }
        return parent::handle($handler);
    }
}