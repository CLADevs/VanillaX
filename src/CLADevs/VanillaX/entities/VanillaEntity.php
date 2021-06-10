<?php

namespace CLADevs\VanillaX\entities;

use CLADevs\VanillaX\network\gamerules\GameRule;
use CLADevs\VanillaX\VanillaX;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;

abstract class VanillaEntity extends Living{

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
    const GOAT = 128;
    const GLOW_SQUID = 129;
    const AXOLOTL = 130;

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
        self::GOAT => "minecraft:goat",
        self::GLOW_SQUID => "minecraft:glow_squid",
        self::AXOLOTL => "minecraft:axolotl"
    ];

    const ARTHROPODS = [
        self::BEE, self::CAVE_SPIDER, self::ENDERMITE,
        self::SILVERFISH, self::SPIDER
    ];

    const UNDEAD = [
        self::DROWNED, self::HUSK, self::PHANTOM,
        self::SKELETON, self::SKELETON_HORSE, self::STRAY,
        self::WITHER, self::WITHER_SKELETON, self::ZOGLIN,
        self::ZOMBIE, self::ZOMBIE_HORSE, self::ZOMBIE_VILLAGER,
        self::ZOMBIE_PIGMAN
    ];

    protected ?Entity $killer = null;

    /**
     * @param int[] {min, max}
     */
    protected function setRangeHealth(array $rangeHealth): void{
        $max = $rangeHealth[1];
        $this->setMaxHealth($max);
    }

    public function getLootName(): string{
        return strtolower(str_replace(" ", "_", $this->getName()));
    }

    public function getXpDropAmount(): int{
        return 0; //TODO
    }

    public function getDrops(): array{
        $loot = [];

        if(GameRule::getGameRuleValue(GameRule::DO_MOB_LOOT, $this->getLevel())){
            if($lootTable = VanillaX::getInstance()->getEntityManager()->getLootManager()->getLootTableFor($this->getLootName())){
                foreach($lootTable->getPools() as $pool){
                    foreach($pool->getEntries() as $entry){
                        $loot[] = $entry->apply($this->killer);
                    }
                }
            }
        }
        return $loot;
    }

    public function getKillerEnchantment(Entity $killer, int $enchantment = Enchantment::LOOTING): int{
        if($killer instanceof Player){
            $held = $killer->getInventory()->getItemInHand();

            if(($level = $held->getEnchantmentLevel($enchantment)) > ($maxLevel = Enchantment::getEnchantment($enchantment)->getMaxLevel())){
                return $maxLevel;
            }
            return $level;
        }
        return 0;
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

    protected function sendSpawnPacket(Player $player): void{
        $pk = new AddActorPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->type = AddActorPacket::LEGACY_ID_MAP_BC[static::NETWORK_ID] ?? self::LEGACY_ID_MAP_BC[static::NETWORK_ID];
        $pk->position = $this->asVector3();
        $pk->motion = $this->getMotion();
        $pk->yaw = $this->yaw;
        $pk->headYaw = $this->yaw;
        $pk->pitch = $this->pitch;
        $pk->attributes = $this->attributeMap->getAll();
        $pk->metadata = $this->propertyManager->getAll();
        $player->dataPacket($pk);

        $this->armorInventory->sendContents($player);
    }
}