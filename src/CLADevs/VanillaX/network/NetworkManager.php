<?php

namespace CLADevs\VanillaX\network;

use CLADevs\VanillaX\network\protocol\ActorEventPacketX;
use CLADevs\VanillaX\network\protocol\ActorPickupRequestPacketX;
use CLADevs\VanillaX\network\protocol\AnvilDamagePacketX;
use CLADevs\VanillaX\network\protocol\CommandBlockUpdatePacketX;
use CLADevs\VanillaX\network\protocol\ContainerClosePacketX;
use CLADevs\VanillaX\network\protocol\FilterTextPacketX;
use CLADevs\VanillaX\network\protocol\InteractPacketX;
use CLADevs\VanillaX\network\protocol\PlayerActionPacketX;
use CLADevs\VanillaX\network\protocol\PlayerInputPacketX;
use pocketmine\network\mcpe\protocol\PacketPool;

class NetworkManager{

    public function startup(): void{
        PacketPool::getInstance()->registerPacket(new FilterTextPacketX());
        PacketPool::getInstance()->registerPacket(new AnvilDamagePacketX());
        PacketPool::getInstance()->registerPacket(new ActorEventPacketX());
        PacketPool::getInstance()->registerPacket(new PlayerActionPacketX());
        PacketPool::getInstance()->registerPacket(new CommandBlockUpdatePacketX());
        PacketPool::getInstance()->registerPacket(new PlayerInputPacketX());
        PacketPool::getInstance()->registerPacket(new ActorPickupRequestPacketX());
        PacketPool::getInstance()->registerPacket(new InteractPacketX());
        PacketPool::getInstance()->registerPacket(new ContainerClosePacketX());
    }
}