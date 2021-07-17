<?php

namespace CLADevs\VanillaX\world\weather;

use CLADevs\VanillaX\VanillaX;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\Tag;
use pocketmine\world\format\io\data\BedrockWorldData;
use pocketmine\world\format\io\WritableWorldProvider;
use pocketmine\world\World;

class Weather{

    const TAG_WEATHER = "WeatherData";
    const TAG_DURATION = "duration";
    const TAG_DELAY_DURATION = "DelayDuration";
    const TAG_THUNDERING = "Thundering";

    private World $world;

    private bool $raining = false;
    private bool $thundering = false;

    public int $duration = 0;
    public int $delayDuration = 0;

    public function __construct(World $world){
        $this->world = $world;
        $provider = $world->getProvider();

        $this->recalculateDelayDuration();

        if($provider instanceof WritableWorldProvider){
            $data = $provider->getWorldData();
            
            if($data instanceof BedrockWorldData){
                $nbt = $data->getCompoundTag();

                if(($tag = $nbt->getTag(self::TAG_WEATHER)) !== null){
                    $tag = $tag->getValue();

                    if(is_array($tag)){
                        /**
                         * @var string $key
                         * @var Tag $v
                         */
                        foreach($tag as $key => $v){
                            switch($key){
                                case self::TAG_DURATION:
                                    $this->duration = $v->getValue();
                                    break;
                                case self::TAG_DELAY_DURATION:
                                    $this->delayDuration = $v->getValue();
                                    break;
                                case self::TAG_THUNDERING:
                                    $this->thundering = $v->getValue();
                                    break;
                            }
                        }
                    }
                    if($this->duration >= 1){
                        $this->startStorm($this->thundering, $this->duration);
                    }
                }
            }
        }
    }

    public function getWorld(): World{
        return $this->world;
    }

    public function isRaining(): bool{
        return $this->raining;
    }

    public function isThundering(): bool{
        return $this->thundering;
    }

    public function saveData(): void{
        if($this->world->isLoaded()){
            return;
        }
        $provider = $this->world->getProvider();

        if($provider instanceof WritableWorldProvider){
            $data = $provider->getWorldData();

            if($data instanceof BedrockWorldData){
                $nbt = $data->getCompoundTag();
                $tag = new CompoundTag();
                $tag->setInt(self::TAG_DURATION, $this->duration);
                $tag->setInt(self::TAG_DELAY_DURATION, $this->delayDuration);
                $tag->setByte(self::TAG_THUNDERING, $this->thundering);
                $nbt->setTag(self::TAG_WEATHER, $tag);
                $data->save();
            }
        }
    }

    public function startStorm(bool $thunder = false, int $duration = null): void{
        WeatherManager::getInstance()->sendWeather(null, $thunder);
        $this->duration = $duration == null ? mt_rand(600, 1200) : $duration;
        $this->raining = true;
        $this->thundering = $thunder;
        $this->saveData();
    }

    public function stopStorm(): void{
        WeatherManager::getInstance()->sendClear(null, $this->thundering);
        $this->recalculateDelayDuration();
        $this->duration = 0;
        $this->raining = false;
        $this->thundering = false;
        $this->saveData();
    }

    public function recalculateDelayDuration(): void{
        $this->delayDuration = mt_rand(600, 9000);
    }
}