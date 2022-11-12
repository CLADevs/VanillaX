<?php

namespace CLADevs\VanillaX\entities;

use CLADevs\VanillaX\configuration\features\MobFeature;
use CLADevs\VanillaX\entities\utils\EntityClassification;
use CLADevs\VanillaX\entities\utils\EntityInfo;
use CLADevs\VanillaX\world\gamerule\GameRule;
use CLADevs\VanillaX\world\gamerule\GameRuleManager;
use pocketmine\entity\Attribute;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\types\entity\Attribute as NetworkAttribute;

abstract class VanillaEntity extends Living{

    const VILLAGER_V2 = 115;

    private bool $baby = false;

    protected function syncNetworkData(EntityMetadataCollection $properties): void{
        parent::syncNetworkData($properties);
        $properties->setGenericFlag(EntityMetadataFlags::BABY, $this->baby);
    }

    public function isOnFire(): bool{
        $parent = parent::isOnFire();

        if(!$parent){
            if(($killer = $this->getLastHitPlayer()) !== null){
                return $killer->getInventory()->getItemInHand()->hasEnchantment(VanillaEnchantments::FIRE_ASPECT());
            }else{
                return ($cause = $this->getLastDamageCause()) instanceof EntityDamageEvent && $cause->getCause() === EntityDamageEvent::CAUSE_FIRE_TICK;
            }
        }
        return true;
    }

    protected function onDeath(): void{
        $ev = new EntityDeathEvent($this, $this->getDrops(), $this->getXpDropAmount());
        $ev->call();
        if(GameRuleManager::getInstance()->getValue(GameRule::DO_MOB_LOOT, $this->getWorld())){
            foreach($ev->getDrops() as $item){
                $this->getWorld()->dropItem($this->getPosition(), $item);
            }
            $this->getWorld()->dropExperience($this->getPosition(), $ev->getXpDropAmount());
        }
        $this->startDeathAnimation();
    }

    protected function sendSpawnPacket(Player $player): void{
        $player->getNetworkSession()->sendDataPacket(AddActorPacket::create(
            $this->getId(),
            $this->getId(),
            static::getNetworkTypeId(),
            $this->getPosition(),
            $this->getMotion(),
            $this->location->pitch,
            $this->location->yaw,
            $this->location->yaw,
            $this->location->yaw,
            array_map(function(Attribute $attr) : NetworkAttribute{
                return new NetworkAttribute($attr->getId(), $attr->getMinValue(), $attr->getMaxValue(), $attr->getValue(), $attr->getDefaultValue(), []);
            }, $this->attributeMap->getAll()),
            $this->getAllNetworkData(),
            new PropertySyncData([], []),
            []
        ));
    }

    /**
     * @param int[] 
     */
    protected function setRangeHealth(array $rangeHealth): void{
        $max = $rangeHealth[1];
        $this->setMaxHealth($max);
    }

    public function getClassification(): int{
        return EntityClassification::NONE;
    }

    public function getLastHitByPlayer(): bool{
        return $this->getLastHitPlayer() !== null;
    }

    public function getLastHitPlayer(): ?Player{
        $cause = $this->getLastDamageCause();

        if($cause instanceof EntityDamageByEntityEvent){
            $killer = $cause->getDamager();

            if($killer instanceof Player){
                return $killer;
            }
        }
        return null;
    }

    public function getEntityInfo(): ?EntityInfo{
        return EntityManager::getInstance()->getEntityInfo(self::getNetworkTypeId());
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return static::NETWORK_ID;
    }

    public function isBaby(): bool{
        return $this->baby;
    }

    public static function canRegister(): bool{
        return MobFeature::getInstance()->isMobEnabled(str_replace("minecraft:", "", self::getNetworkTypeId()));
    }
}
