<?php

namespace CLADevs\VanillaX\listeners\types;

use CLADevs\VanillaX\items\ItemIdentifiers;
use CLADevs\VanillaX\items\types\ShieldItem;
use CLADevs\VanillaX\network\gamerules\GameRule;
use CLADevs\VanillaX\VanillaX;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\entity\object\PrimedTNT;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;

class EntityListener implements Listener{

    public function onDamage(EntityDamageEvent $event): void{
        if(!$event->isCancelled()){
            $entity = $event->getEntity();

            VanillaX::getInstance()->getEnchantmentManager()->handleDamage($event); /** Enchantment Damage */
            switch($event->getCause()){
                case EntityDamageEvent::CAUSE_FALL:
                    if($this->handleFallDamage($entity)) $event->setCancelled();
                    break;
                case EntityDamageEvent::CAUSE_DROWNING:
                    /** If gamerule 'drowningDamage' is not on then cancel it */
                    if(!GameRule::getGameRuleValue(GameRule::DROWNING_DAMAGE, $entity->getLevel())){
                        $event->setCancelled();
                    }
                    break;
                case EntityDamageEvent::CAUSE_FIRE:
                    /** If gamerule 'fireDamage' is not on then cancel it */
                    if(!GameRule::getGameRuleValue(GameRule::FIRE_DAMAGE, $entity->getLevel())){
                        $event->setCancelled();
                    }
                    break;
                case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
                    if($event instanceof EntityDamageByEntityEvent && $entity instanceof Player){
                        /** If gamerule 'pvp' is not on then cancel it */
                        if(!GameRule::getGameRuleValue(GameRule::PVP, $entity->getLevel())){
                            $event->setCancelled();
                            return;
                        }
                        $item = $entity->getInventory()->getItemInHand();
                        if($entity->isSneaking() && $this->handleShieldDamage($event->getDamager(), $entity, $item)){
                            $event->setCancelled();
                        }
                    }
                    break;
            }
            if($entity instanceof Player && ($entity->getHealth() - $event->getFinalDamage()) <= 0 && !$event->isCancelled()){
                $inventory = $entity->getInventory();
                $itemIndex = 0;
                $hasTotem = true;

                if($inventory->getItemInHand()->getId() !== ItemIds::TOTEM){
                    $inventory = VanillaX::getInstance()->getSessionManager()->get($entity)->getOffHandInventory();

                    if($inventory->getItem(0)->getId() !== ItemIds::TOTEM){
                        $hasTotem = false;
                    }
                }else{
                    $itemIndex = $inventory->getHeldItemIndex();
                }
                if($hasTotem){
                    $event->setCancelled();
                    $entity->setHealth(1);
                    $entity->removeAllEffects();

                    $entity->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), 40 * 20, 1));
                    $entity->addEffect(new EffectInstance(Effect::getEffect(Effect::FIRE_RESISTANCE), 40 * 20, 1));
                    $entity->addEffect(new EffectInstance(Effect::getEffect(Effect::ABSORPTION), 5 * 20, 1));

                    $entity->broadcastEntityEvent(ActorEventPacket::CONSUME_TOTEM);
                    $entity->getLevel()->broadcastLevelEvent($entity->add(0, $entity->getEyeHeight(), 0), LevelEventPacket::EVENT_SOUND_TOTEM);

                    $item = $inventory->getItem($itemIndex);
                    $item->pop();
                    $inventory->setItem($itemIndex, $item);
                }
            }
            if($event instanceof EntityDamageByEntityEvent && $entity instanceof Player){
                $resist = 1;

                foreach($entity->getArmorInventory()->getContents(true) as $item){
                    if(in_array($item->getId(), [ItemIdentifiers::NETHERITE_HELMET, ItemIdentifiers::NETHERITE_CHESTPLATE, ItemIdentifiers::NETHERITE_LEGGINGS, ItemIdentifiers::NETHERITE_BOOTS])){
                        $resist += 2;
                    }
                }
                if($resist < 0){
                    $resist = 1;
                }elseif($resist > 1){
                    $resist -= 1;
                }
                $event->setKnockBack($event->getKnockBack() / $resist);
            }
        }
    }

    public function onRegenerateHealth(EntityRegainHealthEvent $event): void{
        if(!$event->isCancelled() && !GameRule::getGameRuleValue(GameRule::NATURAL_REGENERATION, $event->getEntity()->getLevel())){
            $event->setCancelled();
        }
    }

    public function onEntitySpawn(EntitySpawnEvent $event): void{
        $entity = $event->getEntity();

        if($entity instanceof PrimedTNT && !GameRule::getGameRuleValue(GameRule::TNT_EXPLODES, $entity->getLevel())){
            $entity->flagForDespawn();
        }
    }

    private function handleShieldDamage(Entity $damager, Player $entity, Item $item): bool{
        if($damager instanceof Living){
            $offhand = VanillaX::getInstance()->getSessionManager()->get($entity)->getOffHandInventory();
            $minYaw = $entity->yaw - 90;
            $maxYaw = $entity->yaw + 90;
            $xDist = $damager->x - $entity->x;
            $zDist = $damager->z - $entity->z;
            $yaw = atan2($zDist, $xDist) / M_PI * 180 - 90;

            if($yaw < 0){
                $yaw += 360.0;
            }

            if(($offhandItem = $offhand->getItem(0)) instanceof ShieldItem){
                $item = $offhandItem;
            }elseif(!$item instanceof ShieldItem){
                return false;
            }
            if($yaw >= $minYaw && $yaw <= $maxYaw){
                $item->applyDamage(1);
                if($offhandItem instanceof ShieldItem){
                    $offhand->setItem(0, $item);
                }else{
                    $entity->getInventory()->setItemInHand($item);
                }
                $entity->getLevel()->broadcastLevelSoundEvent($entity, LevelSoundEventPacket::SOUND_ITEM_SHIELD_BLOCK);
                $damager->knockBack($entity, 0, $damager->x - $entity->x, $damager->z - $entity->z, 0.5);
                return true;
            }
        }
        return false;
    }

    private function handleFallDamage(Entity $entity): bool{
        $value = false;

        /** If gamerule 'fallDamage' is not on then cancel it */
        if(!GameRule::getGameRuleValue(GameRule::FALL_DAMAGE, $entity->getLevel())){
            $value = true;
        }
        return $value;
    }
}
