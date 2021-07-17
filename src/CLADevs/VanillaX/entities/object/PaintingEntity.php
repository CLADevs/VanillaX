<?php

namespace CLADevs\VanillaX\entities\object;

use CLADevs\VanillaX\world\gamerule\GameRule;
use CLADevs\VanillaX\world\gamerule\GameRuleManager;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\entity\object\Painting;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\world\particle\BlockBreakParticle;

class PaintingEntity extends Painting implements NonAutomaticCallItemTrait{

    const NETWORK_ID = EntityIds::PAINTING;

    public function kill(): void{
        if(!$this->isAlive()){
            return;
        }
        parent::kill();

        $drops = true;

        if($this->lastDamageCause instanceof EntityDamageByEntityEvent){
            $killer = $this->lastDamageCause->getDamager();
            if($killer instanceof Player && $killer->isCreative()){
                $drops = false;
            }
        }

        if($drops && GameRuleManager::getInstance()->getValue(GameRule::DO_ENTITY_DROPS, $this->getWorld())){
            $this->getWorld()->dropItem($this->getPosition(), ItemFactory::getInstance()->get(ItemIds::PAINTING));
        }
        $this->getWorld()->addParticle($this->getPosition()->add(0.5, 0.5, 0.5), new BlockBreakParticle(BlockFactory::getInstance()->get(BlockLegacyIds::PLANKS)));
    }
}