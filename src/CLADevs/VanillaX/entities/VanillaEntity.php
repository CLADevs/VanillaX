<?php

namespace CLADevs\VanillaX\entities;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\world\gamerule\GameRule;
use CLADevs\VanillaX\world\gamerule\GameRuleManager;
use pocketmine\entity\Attribute;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\types\entity\Attribute as NetworkAttribute;

abstract class VanillaEntity extends Living{

    const RAVAGER = 59;
    const PILLAGER = 114;
    const VILLAGER_V2 = 115;
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
        self::AXOLOTL => "minecraft:axolotl",
        self::VILLAGER_V2 => "minecraft:villager_v2",
    ];

    private bool $baby = false;

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
        return $this->baby;
    }

    public function getClassification(): int{
        return EntityClassification::NONE;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return static::NETWORK_ID;
    }

    public function getLastHitByPlayer(): bool{
        $cause = $this->getLastDamageCause();
        return $cause instanceof EntityDamageByEntityEvent && $cause->getDamager() instanceof Player;
    }

    protected function syncNetworkData(EntityMetadataCollection $properties): void{
        parent::syncNetworkData($properties);
        $properties->setGenericFlag(EntityMetadataFlags::BABY, $this->baby);
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
    }

    protected function sendSpawnPacket(Player $player): void{
        $pk = new AddActorPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->type = static::NETWORK_ID;
        $pk->position = $this->getPosition();
        $pk->motion = $this->getMotion();
        $pk->yaw = $this->location->yaw;
        $pk->headYaw = $this->location->yaw;
        $pk->pitch = $this->location->pitch;
        $pk->attributes = array_map(function(Attribute $attr): NetworkAttribute{
            return new NetworkAttribute($attr->getId(), $attr->getMinValue(), $attr->getMaxValue(), $attr->getValue(), $attr->getDefaultValue());
        }, $this->attributeMap->getAll());
        $pk->metadata = $this->getAllNetworkData();
        $player->getNetworkSession()->sendDataPacket($pk);
    }
}
