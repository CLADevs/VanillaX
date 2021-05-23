<?php

namespace CLADevs\VanillaX\network\gamerules;

use CLADevs\VanillaX\VanillaX;
use pocketmine\level\format\io\BaseLevelProvider;
use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\NamedTag;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\Player;
use pocketmine\Server;

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

    /** @var GameRule[] */
    public static array $gameRules = [];

    private string $name;
    private int $type;

    /** @var bool|int */
    private $value;

    public function __construct(string $name, $value, int $type = self::TYPE_BOOL){
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
    }

    public static function init(): void{
        self::register(new GameRule(self::COMMAND_BLOCKS_ENABLED, true)); //TODO
        self::register(new GameRule(self::COMMAND_BLOCK_OUTPUT, true)); //TODO
        self::register(new DoDayLightCycleRule());
        self::register(new GameRule(self::DO_ENTITY_DROPS, true));
        self::register(new GameRule(self::DO_FIRE_TICK, true));
        self::register(new GameRule(self::DO_INSOMNIA, true)); //TODO
        self::register(new GameRule(self::DO_IMMEDIATE_RESPAWN, false));
        self::register(new GameRule(self::DO_MOB_LOOT, true));
        self::register(new GameRule(self::DO_MOB_SPAWNING, true)); //TODO
        self::register(new GameRule(self::DO_TILE_DROPS, true));
        self::register(new GameRule(self::DO_WEATHER_CYCLE, true)); //TODO
        self::register(new GameRule(self::DROWNING_DAMAGE, true));
        self::register(new GameRule(self::FALL_DAMAGE, true));
        self::register(new GameRule(self::FIRE_DAMAGE, true));
        self::register(new GameRule(self::FREEZE_DAMAGE, true)); //TODO
        self::register(new GameRule(self::FUNCTION_COMMAND_LIMIT, 10000, self::TYPE_INT)); //TODO
        self::register(new GameRule(self::KEEP_INVENTORY, false));
        self::register(new GameRule(self::MAX_COMMAND_CHAIN_LENGTH, 65536, self::TYPE_INT)); //TODO
        self::register(new GameRule(self::MOB_GRIEFING, true)); //TODO
        self::register(new GameRule(self::NATURAL_REGENERATION, true));
        self::register(new GameRule(self::PVP, true));
        self::register(new GameRule(self::RANDOM_TICK_SPEED, 1, self::TYPE_INT)); //TODO
        self::register(new GameRule(self::SEND_COMMAND_FEEDBACK, true)); //TODO
        self::register(new GameRule(self::SHOW_COORDINATES, false));
        self::register(new GameRule(self::SHOW_DEATH_MESSAGES, true)); //TODO
        self::register(new GameRule(self::SPAWN_RADIUS, 5, self::TYPE_INT)); //TODO
        self::register(new GameRule(self::TNT_EXPLODES, true));
        self::register(new GameRule(self::SHOW_TAGS, true)); //TODO

        if(!self::isGameRuleAllow() && VanillaX::getInstance()->getConfig()->get("gamerule-remove-cache", true)){
            foreach(Server::getInstance()->getLevels() as $level){
                $provider = $level->getProvider();

                if($provider instanceof BaseLevelProvider){
                    /** @var CompoundTag $nbt */
                    $nbt = $provider->getLevelData()->getTag("GameRules");

                    foreach(self::$gameRules as $rule){
                        if($nbt->hasTag($rule->getName())){
                            $tag = $nbt->getTag($rule->getName());

                            if($rule->getType() === self::TYPE_BOOL && $tag instanceof ByteTag){
                                $nbt->removeTag($rule->getName());
                            }elseif($rule->getType() === self::TYPE_INT && $tag instanceof IntTag){
                                $nbt->removeTag($rule->getName());
                            }
                        }
                    }
                }
            }
        }
    }

    private static function register(GameRule $rule): void{
        self::$gameRules[strtolower($rule->getName())] = $rule;
    }

    /**
     * @param Level $level
     * @param GameRule $rule
     * @param int|bool $value
     * @param bool $force
     */
    public static function setGameRule(Level $level, GameRule $rule, $value, bool $force = false): void{
        if(!$force && !self::isGameRuleAllow()){
            return;
        }
        $provider = $level->getProvider();

        if($provider instanceof BaseLevelProvider){
            /** @var CompoundTag $nbt */
            $nbt = $provider->getLevelData()->getTag("GameRules");
            if($nbt->hasTag($rule->getName())) $nbt->removeTag($rule->getName());
            if(is_bool($value)){
                $nbt->setByte($rule->getName(), $value);
            }else{
                $nbt->setInt($rule->getName(), $value);
            }
            $provider->saveLevelData();
        }
    }

    public static function fixGameRule(Player $player, Level $level = null): void{
        if(!self::isGameRuleAllow()){
            return;
        }
        if($level === null){
            $level = $player->getLevel();
        }
        $provider = $level->getProvider();

        if($provider instanceof BaseLevelProvider){
            /** @var CompoundTag $nbt */
            $nbt = $provider->getLevelData()->getTag("GameRules");

            foreach($nbt->getValue() as $key => $tag){
                $pk = new GameRulesChangedPacket();
                $pk->gameRules = [$key => [$tag instanceof ByteTag ? 1 : 0, $tag->getValue()]];
                $player->dataPacket($pk);
            }
        }
    }

    /**
     * @param string $name
     * @param Level $level
     * @param bool $stringify
     * @return bool|int|string|null
     */
    public static function getGameRuleValue(string $name, Level $level, bool $stringify = false){
        $name = strtolower($name);
        $rule = self::$gameRules[$name] ?? null;
        $provider = $level->getProvider();

        if($rule !== null && !self::isGameRuleAllow()) return $rule->getValue();
        if($rule !== null && $provider instanceof BaseLevelProvider){
            $tag = $provider->getLevelData()->getTag("GameRules")->getValue()[$rule->getName()] ?? null;

            if($tag instanceof NamedTag){
                if($stringify && $tag instanceof ByteTag){
                    return boolval($tag->getValue()) ? "true" : "false";
                }
                return $tag instanceof ByteTag ? boolval($tag->getValue()) : intval($tag->getValue());
            }
        }
        if($stringify && is_bool($rule->getValue())){
            return $rule->getValue() ? "true" : "false";
        }
        return $rule === null ? null : $rule->getValue();
    }

    public static function isGameRuleAllow(): bool{
        return boolval(VanillaX::getInstance()->getConfig()->getNested("features.gamerule", true));
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
    public function getValue(){
        return $this->value;
    }

    /**
     * @param bool|int $value
     * @param Level $level
     */
    public function handleValue($value, Level $level): void{
    }
}