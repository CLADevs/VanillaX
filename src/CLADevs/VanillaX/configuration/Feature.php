<?php

namespace CLADevs\VanillaX\configuration;

use CLADevs\VanillaX\VanillaX;
use pocketmine\data\bedrock\LegacyToStringBidirectionalIdMap;
use pocketmine\utils\Config;
use ReflectionProperty;

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

    public static function addLegacy(LegacyToStringBidirectionalIdMap $instance, string $name, int $id): void{
        $property = new ReflectionProperty(LegacyToStringBidirectionalIdMap::class, "legacyToString");
        $property->setAccessible(true);
        $value = $property->getValue($instance);
        $value[$id] = $name;
        $property->setValue($instance, $value);

        $property = new ReflectionProperty(LegacyToStringBidirectionalIdMap::class, "stringToLegacy");
        $property->setAccessible(true);
        $value = $property->getValue($instance);
        $value[$name] = $id;
        $property->setValue($instance, $value);
    }
}