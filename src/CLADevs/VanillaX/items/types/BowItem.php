<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\VanillaX;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Arrow as ArrowEntity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\item\Bow;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\player\Player;

class BowItem extends Bow{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIds::BOW, 0), "Bow");
    }

    public function onReleaseUsing(Player $player) : ItemUseResult{
        $inventory = VanillaX::getInstance()->getSessionManager()->get($player)->getOffHandInventory();

        if($player->isSurvival() && !$inventory->contains($arrowItem = ItemFactory::getInstance()->get(Item::ARROW, 0, 1))){
            $inventory = $player->getInventory();

            if(!$inventory->contains($arrowItem)){
                $inventory->sendContents($player);
                return false;
            }
        }

        $nbt = Entity::createBaseNBT(
            $player->add(0, $player->getEyeHeight(), 0),
            $player->getDirectionVector(),
            ($player->yaw > 180 ? 360 : 0) - $player->yaw,
            -$player->pitch
        );

        $diff = $player->getItemUseDuration();
        $p = $diff / 20;
        $baseForce = min((($p ** 2) + $p * 2) / 3, 1);

        $entity = Entity::createEntity("Arrow", $player->getLevelNonNull(), $nbt, $player, $baseForce >= 1);
        if($entity instanceof Projectile){
            $infinity = $this->hasEnchantment(Enchantment::INFINITY);
            if($entity instanceof ArrowEntity){
                if($infinity){
                    $entity->setPickupMode(ArrowEntity::PICKUP_CREATIVE);
                }
                if(($punchLevel = $this->getEnchantmentLevel(Enchantment::PUNCH)) > 0){
                    $entity->setPunchKnockback($punchLevel);
                }
            }
            if(($powerLevel = $this->getEnchantmentLevel(Enchantment::POWER)) > 0){
                $entity->setBaseDamage($entity->getBaseDamage() + (($powerLevel + 1) / 2));
            }
            if($this->hasEnchantment(Enchantment::FLAME)){
                $entity->setOnFire(intdiv($entity->getFireTicks(), 20) + 100);
            }
            $ev = new EntityShootBowEvent($player, $this, $entity, $baseForce * 3);

            if($baseForce < 0.1 or $diff < 5 or $player->isSpectator()){
                $ev->setCancelled();
            }

            $ev->call();

            $entity = $ev->getProjectile(); //This might have been changed by plugins

            if($ev->isCancelled()){
                $entity->flagForDespawn();
                $inventory->sendContents($player);
            }else{
                $entity->setMotion($entity->getMotion()->multiply($ev->getForce()));
                if($player->isSurvival()){
                    if(!$infinity){ //TODO: tipped arrows are still consumed when Infinity is applied
                        $inventory->removeItem(ItemFactory::getInstance()->get(Item::ARROW, 0, 1));
                    }
                    $this->applyDamage(1);
                }

                if($entity instanceof Projectile){
                    $projectileEv = new ProjectileLaunchEvent($entity);
                    $projectileEv->call();
                    if($projectileEv->isCancelled()){
                        $ev->getProjectile()->flagForDespawn();
                    }else{
                        $ev->getProjectile()->spawnToAll();
                        $player->getLevelNonNull()->broadcastLevelSoundEvent($player, LevelSoundEventPacket::SOUND_BOW);
                    }
                }else{
                    $entity->spawnToAll();
                }
            }
        }else{
            $entity->spawnToAll();
        }

        return true;
    }
}