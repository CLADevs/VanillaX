<?php

namespace CLADevs\VanillaX\weather;

use CLADevs\VanillaX\entities\object\LightningBoltEntity;
use CLADevs\VanillaX\network\gamerules\GameRule;
use CLADevs\VanillaX\VanillaX;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;

class WeatherManager{

    /** @var Weather[] */
    private array $weathers = [];

    public function startup(): void{
        if(!VanillaX::getInstance()->getConfig()->getNested("features.weather", true)){
            return;
        }
        foreach(Server::getInstance()->getWorldManager()->getWorlds() as $world){
            if(!GameRule::getGameRuleValue(GameRule::DO_WEATHER_CYCLE, $world)){
                continue;
            }
            $this->addWeather($level);
        }
        VanillaX::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function(): void{
            foreach($this->weathers as $weather){
                if($weather->isRaining()){
                    $weather->duration--;

                    if($weather->duration < 1){
                        $weather->stopStorm();
                    }elseif($weather->isThundering() && mt_rand(1, 100000) === 0){
                        $players = Server::getInstance()->getOnlinePlayers();

                        if(count($players) >= 1){
                            $random = $players[array_rand($players)];
                            $pos = $random->add(mt_rand(0, 15), mt_rand(0, 15));
                            $entity = new LightningBoltEntity($weather->getLevel(), LightningBoltEntity::createBaseNBT($pos));
                            $entity->spawnToAll();
                        }
                    }
                }else{
                    $weather->delayDuration--;

                    if($weather->delayDuration < 1){
                        $weather->startStorm();
                    }
                }
                $weather->saveData();
            }
        }), 20);
    }

    public function addWeather(Level $level): void{
        $this->weathers[strtolower($level->getFolderName())] = new Weather($level);
    }

    public function removeWeather(Level $level): void{
        if(isset($this->weathers[strtolower($level->getFolderName())])){
            unset($this->weathers[strtolower($level->getFolderName())]);
        }
    }

    /**
     * @param Level|string $level
     * @return Weather|null
     */
    public function getWeather($level): ?Weather{
        $levelName = $level;

        if($level instanceof Level){
            $levelName = $level->getFolderName();

            if(!isset($this->weathers[strtolower($levelName)])){
                $this->addWeather($level);
            }
        }
        return $this->weathers[strtolower($levelName)] ?? null;
    }

    public function isRaining(Level $level, bool $checkThunder = true): bool{
        $weather = $this->weathers[strtolower($level->getFolderName())] ?? null;

        if($weather !== null){
            return $weather->isRaining() ? true : ($checkThunder ? $weather->isThundering() : false);
        }
        return false;
    }

    public function isThundering(Level $level): bool{
        $weather = $this->weathers[strtolower($level->getFolderName())] ?? null;

        if($weather !== null && $weather->isThundering()){
            return true;
        }
        return false;
    }

    /**
     * @param Player[]|Player|null $player
     * @param bool $thunder
     */
    public function sendClear($player = null, bool $thunder = false): void{
        if($player === null){
            $player = Server::getInstance()->getOnlinePlayers();
        }elseif($player instanceof Player){
            $player = [$player];
        }
        foreach($player as $p){
            $pk = new LevelEventPacket();
            $pk->evid = LevelEventPacket::EVENT_STOP_RAIN;
            $pk->data = 0;
            $pk->position = new Vector3(0, 0, 0);
            $p->dataPacket($pk);

            if($thunder){
                $pk = new LevelEventPacket();
                $pk->evid = LevelEventPacket::EVENT_STOP_THUNDER;
                $pk->data = 0;
                $pk->position = new Vector3(0, 0, 0);
                $p->dataPacket($pk);
            }
        }
    }

    /**
     * @param Player[]|Player|null $player
     * @param bool $thunder
     */
    public function sendWeather($player = null, bool $thunder = false): void{
        if($player === null){
            $player = Server::getInstance()->getOnlinePlayers();
        }elseif($player instanceof Player){
            $player = [$player];
        }
        foreach($player as $p){
            $pk = new LevelEventPacket();
            $pk->evid = LevelEventPacket::EVENT_START_RAIN;
            $pk->data = 65535;
            $pk->position = new Vector3(0, 0, 0);
            $p->dataPacket($pk);

            if($thunder){
                $pk = new LevelEventPacket();
                $pk->evid = LevelEventPacket::EVENT_START_THUNDER;
                $pk->data = 65535;
                $pk->position = new Vector3(0, 0, 0);
                $p->dataPacket($pk);
            }
        }
    }
}