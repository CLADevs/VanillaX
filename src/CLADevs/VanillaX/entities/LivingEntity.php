<?php

namespace CLADevs\VanillaX\entities;

use CLADevs\VanillaX\entities\utils\EntityAgeable;
use CLADevs\VanillaX\VanillaX;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;

abstract class LivingEntity extends Living{

    const RAVAGER = 59;
    const PILLAGER = 114;
    const WANDERING_TRADER = 118;
    const FOX = 121;
    const BEE = 122;
    const PIGLIN = 123;
    const HOGLIN = 124;
    const STRIDER = 125;
    const ZOGLIN = 126;
    const PIGLIN_BRUTE = 127;

    const LEGACY_ID_MAP_BC = [
        self::RAVAGER => "minecraft:ravager",
        self::PILLAGER => "minecraft:pillager",
        self::WANDERING_TRADER => "minecraft:wandering_trader",
        self::FOX => "minecraft:fox",
        self::BEE => "minecraft:bee",
        self::PIGLIN => "minecraft:piglin",
        self::HOGLIN => "minecraft:hoglin",
        self::STRIDER => "minecraft:strider",
        self::ZOGLIN => "minecraft:zoglin",
        self::PIGLIN_BRUTE => "minecraft:piglin_brute",
    ];

    protected ?EntityAgeable $ageable = null;
    protected ?Entity $killer = null;
    protected bool $initialHealth = false;

    public function getAgeable(): ?EntityAgeable{
        return $this->ageable;
    }

    public function getLootName(): string{
        return strtolower(str_replace(" ", "_", $this->getName()));
    }

    public function getXpDropAmount(): int{
        return 0; //TODO
    }

    public function getDrops(): array{
        $loot = [];

        if($lootTable = VanillaX::getInstance()->getEntityManager()->getLootManager()->getLootTableFor($this->getLootName())){
            foreach($lootTable->getPools() as $pool){
                foreach($pool->getEntries() as $entry){
                    $loot[] = $entry->apply($this->killer);
                }
            }
        }else{
           // var_dump("Unknown Loot table for: " . $this->getLootName());
        }
        return $loot;
    }

    public function getKillerEnchantment(Entity $killer, int $enchantment = Enchantment::LOOTING, bool $bypassMaxCheck = false): int{
        if($killer instanceof Player){
            $held = $killer->getInventory()->getItemInHand();

            if(($level = $held->getEnchantmentLevel($enchantment)) > ($maxLevel = Enchantment::getEnchantment($enchantment)->getMaxLevel())){
                return $maxLevel;
            }
            return $level;
        }
        return 0;
    }

    public function recalculateBoundingBox(): void{
        parent::recalculateBoundingBox();
    }

    public function setMaxHealth(int $amount): void{
        parent::setMaxHealth($amount);
        if(!$this->initialHealth){
            $this->initialHealth = true;
            $this->setHealth($amount);
        }
    }

    public function attack(EntityDamageEvent $source): void{
        if($this->isClosed()) return;
        if(!$source->isCancelled()){
            if(($source->getEntity()->getHealth() - $source->getFinalDamage()) <= 0){
                if($this->killer === null && $source instanceof EntityDamageByEntityEvent){
                    $this->killer = $source->getDamager();
                }
            }
            parent::attack($source);

        }
    }

    public function entityBaseTick(int $tickDiff = 1): bool{
        if($this->isClosed()){
            return false;
        }

        $parent = parent::entityBaseTick($tickDiff);
        if($this->ageable !== null){
            $this->ageable->tick();
        }
        return $parent;
    }

    protected function sendSpawnPacket(Player $player): void{
        $pk = new AddActorPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->type = AddActorPacket::LEGACY_ID_MAP_BC[static::NETWORK_ID] ?? self::LEGACY_ID_MAP_BC[static::NETWORK_ID];
        $pk->position = $this->asVector3();
        $pk->motion = $this->getMotion();
        $pk->yaw = $this->yaw;
        $pk->headYaw = $this->yaw; //TODO
        $pk->pitch = $this->pitch;
        $pk->attributes = $this->attributeMap->getAll();
        $pk->metadata = $this->propertyManager->getAll();
        $player->dataPacket($pk);

        $this->armorInventory->sendContents($player);
    }
}