<?php

namespace CLADevs\VanillaX\entities\object;

use CLADevs\VanillaX\world\gamerule\GameRule;
use CLADevs\VanillaX\world\gamerule\GameRuleManager;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class TNTMinecartEntity extends MinecartEntity{

    const NETWORK_ID = EntityIds::TNT_MINECART;

    public function kill(): void{
        if(GameRuleManager::getInstance()->getValue(GameRule::DO_ENTITY_DROPS, $this->getWorld())){
            $this->getWorld()->dropItem($this->getPosition(), ItemFactory::getInstance()->get(ItemIds::MINECART_WITH_TNT));
        }
        parent::kill();
    }
}