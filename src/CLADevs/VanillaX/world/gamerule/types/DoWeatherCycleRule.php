<?php

namespace CLADevs\VanillaX\world\gamerule\types;

use CLADevs\VanillaX\world\gamerule\GameRule;
use CLADevs\VanillaX\world\weather\WeatherManager;
use pocketmine\world\World;

class DoWeatherCycleRule extends GameRule{

    public function __construct(){
        parent::__construct(self::DO_WEATHER_CYCLE, true);
    }

    public function handleValue($value, World $world): void{
        if($value){
            WeatherManager::getInstance()->addWeather($world);
        }else{
            WeatherManager::getInstance()->removeWeather($world);
        }
    }
}