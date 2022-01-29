<?php

namespace CLADevs\VanillaX\configuration\features;

use CLADevs\VanillaX\configuration\Feature;
use pocketmine\utils\SingletonTrait;

class CommandFeature extends Feature{
    use SingletonTrait;

    /** @var bool[] */
    private array $commands;

    public function __construct(){
        self::setInstance($this);
        parent::__construct("command");
        $this->commands = $this->config->get("commands", []);
    }

    public function isCommandEnable(string $name): bool{
        return $this->commands[$name] ?? false;
    }
}