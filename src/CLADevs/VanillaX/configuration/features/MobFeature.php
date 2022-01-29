<?php

namespace CLADevs\VanillaX\configuration\features;

use CLADevs\VanillaX\configuration\Feature;
use pocketmine\utils\SingletonTrait;

class MobFeature extends Feature{
    use SingletonTrait;

    /** @var bool[] */
    private array $mobs;

    public function __construct(){
        self::setInstance($this);
        parent::__construct("mob");
        $this->mobs = $this->config->get("mobs", []);
    }

    public function isMobEnabled(string $name): bool{
        return $this->mobs[$name] ?? false;
    }
}