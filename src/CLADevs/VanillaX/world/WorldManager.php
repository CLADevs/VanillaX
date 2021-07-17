<?php

namespace CLADevs\VanillaX\world;

use CLADevs\VanillaX\world\gamerule\GameRuleManager;
use CLADevs\VanillaX\world\weather\WeatherManager;
use pocketmine\utils\SingletonTrait;

class WorldManager{
    use SingletonTrait;

    private WeatherManager $weatherManager;
    private GameRuleManager $gameruleManager;

    public function __construct(){
        self::setInstance($this);
        $this->weatherManager = new WeatherManager();
        $this->gameruleManager = new GameRuleManager();
    }

    public function startup(): void{
        $this->weatherManager->startup();
        $this->gameruleManager->startup();
    }

    public function getWeatherManager(): WeatherManager{
        return $this->weatherManager;
    }

    public function getGameruleManager(): GameRuleManager{
        return $this->gameruleManager;
    }
}