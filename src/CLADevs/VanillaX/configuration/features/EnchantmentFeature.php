<?php

namespace CLADevs\VanillaX\configuration\features;

use CLADevs\VanillaX\configuration\Feature;
use pocketmine\utils\SingletonTrait;

class EnchantmentFeature extends Feature{
    use SingletonTrait;

    /** @var bool[] */
    private array $enchantments;

    public function __construct(){
        self::setInstance($this);
        parent::__construct("enchantment");
        $this->enchantments = $this->config->get("enchantments", []);
    }

    public function isEnchantmentEnabled(string $name): bool{
        return $this->enchantments[$name] ?? false;
    }
}