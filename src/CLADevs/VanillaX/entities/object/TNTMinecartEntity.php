<?php

namespace CLADevs\VanillaX\entities\object;

use CLADevs\VanillaX\network\gamerules\GameRule;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class TNTMinecartEntity extends MinecartEntity{

    const NETWORK_ID = self::TNT_MINECART;

    public function kill(): void{
        if(GameRule::getGameRuleValue(GameRule::DO_ENTITY_DROPS, $this->getLevel())){
            $this->getLevel()->dropItem($this, ItemFactory::get(ItemIds::MINECART_WITH_TNT));
        }
        parent::kill();
    }
}