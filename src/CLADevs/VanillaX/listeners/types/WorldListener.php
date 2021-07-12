<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\network\gamerules\GameRule;
use CLADevs\VanillaX\VanillaX;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\world\WorldLoadEvent;
use pocketmine\event\world\WorldUnloadEvent;
use pocketmine\player\Player;

class WorldListener implements Listener{

    public function onLevelLoad(WorldLoadEvent $event): void{
        VanillaX::getInstance()->getWeatherManager()->addWeather($event->getWorld());
    }

    public function onLevelUnload(WorldUnloadEvent $event): void{
        if(!$event->isCancelled()){
            VanillaX::getInstance()->getWeatherManager()->removeWeather($event->getWorld());
        }
    }

    public function onLevelChange(EntityTeleportEvent $event): void{
        $from = $event->getFrom();
        $to = $event->getTo();

        if(!$event->isCancelled() && $from->getWorld()->getFolderName() !== $to->getWorld()->getFolderName()){
            $entity = $event->getEntity();
            $weather = VanillaX::getInstance()->getWeatherManager();
            $previousWeather = $weather->getWeather($from->getWorld());
            $targetWeather = $weather->getWeather($to->getWorld());

            if($entity instanceof Player){
                GameRule::fixGameRule($entity, $to->getWorld());
                if($previousWeather !== null && $targetWeather !== null && $previousWeather->isRaining() && !$targetWeather->isRaining()){
                    $weather->sendClear($entity);
                }
            }
        }
    }
}