<?php

namespace CLADevs\VanillaX;

use CLADevs\VanillaX\blocks\tiles\CommandBlockTile;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;

class VanillaListener implements Listener{

    public function handlePacketReceive(DataPacketReceiveEvent $event): void{
        $packet = $event->getPacket();

        if($packet instanceof CommandBlockUpdatePacket){
            $player = $event->getPlayer();
            $position = new Position($packet->x, $packet->y, $packet->z, $player->getLevel());
            $tile = $position->getLevel()->getTile($position);

            if($tile instanceof CommandBlockTile){
                $tile->handleCommandBlockUpdateReceive($packet);
            }
        }
    }
}