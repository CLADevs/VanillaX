<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\world\gamerule\GameRuleManager;
use CLADevs\VanillaX\world\weather\WeatherManager;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\world\WorldLoadEvent;
use pocketmine\event\world\WorldUnloadEvent;
use pocketmine\player\Player;

class WorldListener implements Listener{

    public function onLevelLoad(WorldLoadEvent $event): void{
        WeatherManager::getInstance()->addWeather($event->getWorld());
    }

    public function onLevelUnload(WorldUnloadEvent $event): void{
        if(!$event->isCancelled()){
            WeatherManager::getInstance()->removeWeather($event->getWorld());
        }
    }

    public function onLevelChange(EntityTeleportEvent $event): void{
        $from = $event->getFrom();
        $to = $event->getTo();

        if(!$event->isCancelled() && $from->getWorld()->getFolderName() !== $to->getWorld()->getFolderName()){
            $entity = $event->getEntity();
            $weather = WeatherManager::getInstance();
            $previousWeather = $weather->getWeather($from->getWorld());
            $targetWeather = $weather->getWeather($to->getWorld());

            if($entity instanceof Player){
                GameRuleManager::getInstance()->sendChanges($entity, $to->getWorld());
                if($previousWeather !== null && $targetWeather !== null && $previousWeather->isRaining() && !$targetWeather->isRaining()){
                    $weather->sendClear($entity);
                }
            }
        }
    }
}