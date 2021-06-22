<?php

namespace CLADevs\VanillaX\network;

use CLADevs\VanillaX\network\gamerules\GameRule;
use CLADevs\VanillaX\network\protocol\ActorEventPacketX;
use CLADevs\VanillaX\network\protocol\ActorPickupRequestPacketX;
use CLADevs\VanillaX\network\protocol\AnvilDamagePacketX;
use CLADevs\VanillaX\network\protocol\CommandBlockUpdatePacketX;
use CLADevs\VanillaX\network\protocol\FilterTextPacketX;
use CLADevs\VanillaX\network\protocol\InventoryTransactionPacketX;
use CLADevs\VanillaX\network\protocol\MobEquipmentPacketX;
use CLADevs\VanillaX\network\protocol\PlayerActionPacketX;
use CLADevs\VanillaX\network\protocol\PlayerInputPacketX;
use pocketmine\network\mcpe\protocol\PacketPool;

class NetworkManager{

    public function startup(): void{
        GameRule::init();

        PacketPool::registerPacket(new InventoryTransactionPacketX());
        PacketPool::registerPacket(new FilterTextPacketX());
        PacketPool::registerPacket(new AnvilDamagePacketX());
        PacketPool::registerPacket(new ActorEventPacketX());
        PacketPool::registerPacket(new PlayerActionPacketX());
        PacketPool::registerPacket(new CommandBlockUpdatePacketX());
        PacketPool::registerPacket(new PlayerInputPacketX());
        PacketPool::registerPacket(new MobEquipmentPacketX());
        PacketPool::registerPacket(new ActorPickupRequestPacketX());
    }
}