<?php

namespace CLADevs\VanillaX\blocks\block\redstone;

use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
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