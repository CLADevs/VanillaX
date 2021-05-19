<?php

namespace CLADevs\VanillaX\network\gamerules;

use CLADevs\VanillaX\network\GameRule;
use pocketmine\level\Level;

class DoDayLightCycleRule extends GameRule{

    public function __construct(){
        parent::__construct(self::DO_DAY_LIGHT_CYCLE, true);
    }

    public function handleValue($value, Level $level): void{
        if($value){
            $level->startTime();
        }else{
            $level->stopTime();
        }
    }
}