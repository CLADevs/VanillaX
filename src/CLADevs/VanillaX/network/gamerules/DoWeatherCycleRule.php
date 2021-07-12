<?php

namespace CLADevs\VanillaX\network\gamerules;

use CLADevs\VanillaX\VanillaX;
use pocketmine\world\World;

class DoWeatherCycleRule extends GameRule{

    public function __construct(){
        parent::__construct(self::DO_WEATHER_CYCLE, true);
    }

    public function handleValue($value, World $world): void{
        if($value){
            VanillaX::getInstance()->getWeatherManager()->addWeather($world);
        }else{
            VanillaX::getInstance()->getWeatherManager()->removeWeather($world);
        }
    }
}