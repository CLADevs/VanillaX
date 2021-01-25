<?php

namespace CLADevs\VanillaX\blocks\types;

use pocketmine\block\Magma;
use pocketmine\block\Water;
use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\item\enchantment\Enchantment;
class MagmaBlock extends Magma{

    public function onEntityCollide(Entity $entity): void{
        //pmmp entity collision for block isnt working either
        if($entity instanceof Living){
            if($entity->hasEffect(Effect::FIRE_RESISTANCE) || $entity->getArmorInventory()->getBoots()->hasEnchantment(Enchantment::FROST_WALKER)){
                return;
            }
        }
        parent::onEntityCollide($entity);
    }

    public function onRandomTick(): void{
        $pos = $this->add(0, 1);

        if($this->getLevel()->getBlock($pos) instanceof Water){
            //brah pmmp random tick is not working
            //            $pk = new LevelEventPacket();
            //            $pk->evid = LevelEventPacket::EVENT_ADD_PARTICLE_MASK | Particle::TYPE_BUBBLE_COLUMN_UP;
            //            $pk->position = $pos;
            //            $pk->data = 0;
            //            $this->getLevel()->broadcastPacketToViewers($pos, $pk);
        }
    }

    public function ticksRandomly(): bool{
        return true;
    }
}