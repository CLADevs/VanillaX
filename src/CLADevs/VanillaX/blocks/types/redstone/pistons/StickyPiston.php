<?php

namespace CLADevs\VanillaX\blocks\types\redstone\pistons;

class StickyPiston extends Piston{

    public function __construct(int $meta = 0){
        parent::__construct($meta);
        $this->id = self::STICKY_PISTON;
        $this->fallbackName = "Sticky Piston";
    }
}