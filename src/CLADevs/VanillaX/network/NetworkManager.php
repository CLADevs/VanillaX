<?php

namespace CLADevs\VanillaX\network;

use CLADevs\VanillaX\network\protocol\ItemStackRequestPacketX;
use pocketmine\network\mcpe\protocol\PacketPool;

class NetworkManager{

    public function __construct(){
        PacketPool::getInstance()->registerPacket(new ItemStackRequestPacketX());
    }
}