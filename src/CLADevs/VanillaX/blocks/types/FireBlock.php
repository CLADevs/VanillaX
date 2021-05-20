<?php

namespace CLADevs\VanillaX\blocks\types;

use CLADevs\VanillaX\network\gamerules\GameRule;
use pocketmine\block\Fire;

class FireBlock extends Fire{

    public function onRandomTick(): void{
        if(!GameRule::getGameRuleValue(GameRule::DO_FIRE_TICK, $this->getLevel())){
            return;
        }
        parent::onRandomTick();
    }
}