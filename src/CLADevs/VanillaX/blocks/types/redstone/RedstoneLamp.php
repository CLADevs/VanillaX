<?php

namespace CLADevs\VanillaX\blocks\types\redstone;

use CLADevs\VanillaX\items\utils\NonAutomaticCallItemTrait;
use pocketmine\block\RedstoneLamp as PMRedstoneLamp;

class RedstoneLamp extends PMRedstoneLamp implements NonAutomaticCallItemTrait{

    private bool $litLamp;

    public function __construct(bool $lit, int $meta = 0){
        parent::__construct($meta);
        $this->litLamp = $lit;

        if($lit){
            $this->id = self::LIT_REDSTONE_LAMP;
            $this->fallbackName = "Lit " . $this->fallbackName;
        }
    }
}