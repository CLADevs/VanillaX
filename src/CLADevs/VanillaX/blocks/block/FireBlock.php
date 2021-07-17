<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\world\gamerule\GameRule;
use CLADevs\VanillaX\world\gamerule\GameRuleManager;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Fire;

class FireBlock extends Fire{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::FIRE, 0), "Fire Block", BlockBreakInfo::instant());
    }

    public function onRandomTick(): void{
        if(!GameRuleManager::getInstance()->getValue(GameRule::DO_FIRE_TICK, $this->getPos()->getWorld())){
            return;
        }
        parent::onRandomTick();
    }
}