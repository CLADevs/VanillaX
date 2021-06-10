<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\network\gamerules\GameRule;
use CLADevs\VanillaX\VanillaX;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\level\LevelUnloadEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class WorldListener implements Listener{

    public function onLevelLoad(LevelLoadEvent $event): void{
        VanillaX::getInstance()->getWeatherManager()->addWeather($event->getLevel());
    }

    public function onLevelUnload(LevelUnloadEvent $event): void{
        if(!$event->isCancelled()){
            VanillaX::getInstance()->getWeatherManager()->removeWeather($event->getLevel());
        }
    }

    public function onLevelChange(EntityLevelChangeEvent $event): void{
        if(!$event->isCancelled()){
            $entity = $event->getEntity();
            $previous = $event->getOrigin();
            $target = $event->getTarget();
            $weather = VanillaX::getInstance()->getWeatherManager();
            $previousWeather = $weather->getWeather($previous);
            $targetWeather = $weather->getWeather($target);

            if($entity instanceof Player){
                GameRule::fixGameRule($entity, $target);
                if($previousWeather !== null && $targetWeather !== null && $previousWeather->isRaining() && !$targetWeather->isRaining()){
                    $weather->sendClear($entity);
                }
            }
        }
    }
}