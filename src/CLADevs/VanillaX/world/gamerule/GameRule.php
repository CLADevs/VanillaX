<?php

namespace CLADevs\VanillaX\world\gamerule;

use pocketmine\world\World;

class GameRule{

    const TYPE_BOOL = 0;
    const TYPE_INT = 1;

    CONST COMMAND_BLOCKS_ENABLED = "commandBlocksEnabled";
    CONST COMMAND_BLOCK_OUTPUT = "commandBlockOutput";
    CONST DO_DAY_LIGHT_CYCLE = "doDaylightCycle";
    CONST DO_ENTITY_DROPS = "doEntityDrops";
    CONST DO_FIRE_TICK = "doFireTick";
    CONST DO_INSOMNIA = "doInsomnia";
    CONST DO_IMMEDIATE_RESPAWN = "doImmediateRespawn";
    CONST DO_MOB_LOOT = "doMobLoot";
    CONST DO_MOB_SPAWNING = "doMobSpawning";
    CONST DO_TILE_DROPS = "doTileDrops";
    CONST DO_WEATHER_CYCLE = "doWeatherCycle";
    CONST DROWNING_DAMAGE = "drowningDamage";
    CONST FALL_DAMAGE = "fallDamage";
    CONST FIRE_DAMAGE = "fireDamage";
    CONST FREEZE_DAMAGE = "freezeDamage";
    CONST FUNCTION_COMMAND_LIMIT = "functionCommandLimit";
    CONST KEEP_INVENTORY = "keepInventory";
    CONST MAX_COMMAND_CHAIN_LENGTH = "maxCommandChainLength";
    CONST MOB_GRIEFING = "mobGriefing";
    CONST NATURAL_REGENERATION = "naturalRegeneration";
    CONST PVP = "pvp";
    CONST RANDOM_TICK_SPEED = "randomTickSpeed";
    CONST SEND_COMMAND_FEEDBACK = "sendCommandFeedback";
    CONST SHOW_COORDINATES = "showCoordinates";
    CONST SHOW_DEATH_MESSAGES = "showDeathMessages";
    CONST SPAWN_RADIUS = "spawnRadius";
    CONST TNT_EXPLODES = "tntExplodes";
    CONST SHOW_TAGS = "showTags";

    private string $name;
    private int $type;

    /** @var bool|int */
    private $defaultValue;

    public function __construct(string $name, $defaultValue, int $type = self::TYPE_BOOL){
        $this->name = $name;
        $this->defaultValue = $defaultValue;
        $this->type = $type;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getType(): int{
        return $this->type;
    }

    /**
     * @return bool|int
     */
    public function getDefaultValue(){
        return $this->defaultValue;
    }

    /**
     * @param bool|int $value
     * @param World $world
     */
    public function handleValue($value, World $world): void{
    }
}