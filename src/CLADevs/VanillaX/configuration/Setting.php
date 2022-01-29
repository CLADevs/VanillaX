<?php

namespace CLADevs\VanillaX\configuration;

use CLADevs\VanillaX\VanillaX;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

final class Setting{
    use SingletonTrait;

    /** @var bool[] */
    private array $features;

    public function __construct(){
        self::setInstance($this);
        VanillaX::getInstance()->saveResource("setting.yml");
        $this->features = (new Config(VanillaX::getInstance()->getDataFolder() . "setting.yml", Config::YAML))->get("features", []);
    }

    public function isWeatherEnabled(): bool{
        return $this->features["weather"] ?? true;
    }

    public function isGameRuleEnabled(): bool{
        return $this->features["gamerule"] ?? true;
    }

    public function isTradeEnabled(): bool{
        return $this->features["trade"] ?? true;
    }
}