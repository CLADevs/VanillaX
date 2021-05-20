<?php

namespace CLADevs\VanillaX\entities\object;

use CLADevs\VanillaX\items\utils\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\network\gamerules\GameRule;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\entity\object\Painting;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\Player;

class PaintingEntity extends Painting implements NonAutomaticCallItemTrait{

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

        if($drops && GameRule::getGameRuleValue(GameRule::DO_ENTITY_DROPS, $this->getLevel())){
            $this->level->dropItem($this, ItemFactory::get(Item::PAINTING));
        }
        $this->level->addParticle(new DestroyBlockParticle($this->add(0.5, 0.5, 0.5), BlockFactory::get(Block::PLANKS)));
    }
}