<?php

namespace CLADevs\VanillaX\entities;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\network\gamerules\GameRule;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDeathEvent;
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

    /**
     * @param int[] 
     */
    protected function setRangeHealth(array $rangeHealth): void{
        $max = $rangeHealth[1];
        $this->setMaxHealth($max);
    }

    public function getLootName(): string{
        return strtolower(str_replace(" ", "_", $this->getName()));
    }

    public function isBaby(): bool{
        return $this->getGenericFlag(self::DATA_FLAG_BABY);
    }

    public function getClassification(): int{
        return EntityClassification::NONE;
    }

    public function getLastHitByPlayer(): bool{
        $cause = $this->getLastDamageCause();
        return $cause instanceof EntityDamageByEntityEvent && $cause->getDamager() instanceof Player;
    }

    protected function onDeath(): void{
        $ev = new EntityDeathEvent($this, $this->getDrops(), $this->getXpDropAmount());
        $ev->call();
        if(GameRule::getGameRuleValue(GameRule::DO_MOB_LOOT, $this->getLevel())){
            foreach($ev->getDrops() as $item){
                $this->getLevelNonNull()->dropItem($this, $item);
            }
            $this->level->dropExperience($this, $ev->getXpDropAmount());
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
