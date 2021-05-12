<?php

namespace CLADevs\VanillaX\network;

use CLADevs\VanillaX\network\protocol\ActorEventPacketX;
use CLADevs\VanillaX\network\protocol\AnvilDamagePacketX;
use CLADevs\VanillaX\network\protocol\FilterTextPacketX;
use CLADevs\VanillaX\network\protocol\InventoryTransactionPacketX;
use pocketmine\network\mcpe\protocol\PacketPool;

class NetworkManager{

    public function startup(): void{
        PacketPool::registerPacket(new InventoryTransactionPacketX());
        PacketPool::registerPacket(new FilterTextPacketX());
        PacketPool::registerPacket(new AnvilDamagePacketX());
        PacketPool::registerPacket(new ActorEventPacketX());
    }
}