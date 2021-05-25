<?php

namespace CLADevs\VanillaX\network\gamerules;

use CLADevs\VanillaX\VanillaX;
use pocketmine\level\Level;

class DoWeatherCycleRule extends GameRule{

    public function __construct(){
        parent::__construct(self::DO_WEATHER_CYCLE, true);
    }

    public function handleValue($value, Level $level): void{
        if($value){
            VanillaX::getInstance()->getWeatherManager()->addWeather($level);
        }else{
            VanillaX::getInstance()->getWeatherManager()->removeWeather($level);
        }
    }
}