<?php

namespace CLADevs\VanillaX\configuration;

use CLADevs\VanillaX\VanillaX;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class Feature{

    protected bool $enabled;

    protected Config $config;

    public function __construct(string $name){
        VanillaX::getInstance()->saveResource("features/$name.yml");
        $this->config = new Config(VanillaX::getInstance()->getDataFolder() . "features/$name.yml", Config::YAML);
        $this->enabled = $this->config->get("enabled", true);
    }

    public function isEnabled(): bool{
        return $this->enabled;
    }

    public function getConfig(): Config{
        return $this->config;
    }
}