<?php

namespace CLADevs\VanillaX\world\gamerule;

use CLADevs\VanillaX\VanillaX;
use CLADevs\VanillaX\world\gamerule\types\DoDayLightCycleRule;
use CLADevs\VanillaX\world\gamerule\types\DoWeatherCycleRule;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\Tag;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocketmine\network\mcpe\protocol\types\IntGameRule;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\format\io\BaseWorldProvider;
use pocketmine\world\format\io\data\BedrockWorldData;
use pocketmine\world\World;

class GameRuleManager{
    use SingletonTrait;

    /** @var GameRule[] */
    public array $gameRules = [];
    
    public function __construct(){
        self::setInstance($this);
        $this->register(new GameRule(GameRule::COMMAND_BLOCKS_ENABLED, true)); //TODO
        $this->register(new GameRule(GameRule::COMMAND_BLOCK_OUTPUT, true)); //TODO
        $this->register(new DoDayLightCycleRule());
        $this->register(new GameRule(GameRule::DO_ENTITY_DROPS, true));
        $this->register(new GameRule(GameRule::DO_FIRE_TICK, true));
        $this->register(new GameRule(GameRule::DO_INSOMNIA, true)); //TODO
        $this->register(new GameRule(GameRule::DO_IMMEDIATE_RESPAWN, false));
        $this->register(new GameRule(GameRule::DO_MOB_LOOT, true));
        $this->register(new GameRule(GameRule::DO_MOB_SPAWNING, true)); //TODO
        $this->register(new GameRule(GameRule::DO_TILE_DROPS, true));
        $this->register(new DoWeatherCycleRule());
        $this->register(new GameRule(GameRule::DROWNING_DAMAGE, true));
        $this->register(new GameRule(GameRule::FALL_DAMAGE, true));
        $this->register(new GameRule(GameRule::FIRE_DAMAGE, true));
        $this->register(new GameRule(GameRule::FREEZE_DAMAGE, true)); //TODO
        $this->register(new GameRule(GameRule::FUNCTION_COMMAND_LIMIT, 10000, GameRule::TYPE_INT)); //TODO
        $this->register(new GameRule(GameRule::KEEP_INVENTORY, false));
        $this->register(new GameRule(GameRule::MAX_COMMAND_CHAIN_LENGTH, 65536, GameRule::TYPE_INT)); //TODO
        $this->register(new GameRule(GameRule::MOB_GRIEFING, true)); //TODO
        $this->register(new GameRule(GameRule::NATURAL_REGENERATION, true));
        $this->register(new GameRule(GameRule::PVP, true));
        $this->register(new GameRule(GameRule::RANDOM_TICK_SPEED, 1, GameRule::TYPE_INT)); //TODO
        $this->register(new GameRule(GameRule::SEND_COMMAND_FEEDBACK, true)); //TODO
        $this->register(new GameRule(GameRule::SHOW_COORDINATES, false));
        $this->register(new GameRule(GameRule::SHOW_DEATH_MESSAGES, true)); //TODO
        $this->register(new GameRule(GameRule::SPAWN_RADIUS, 5, GameRule::TYPE_INT)); //TODO
        $this->register(new GameRule(GameRule::TNT_EXPLODES, true));
        $this->register(new GameRule(GameRule::SHOW_TAGS, true)); //TODO

        if(!$this->isEnabled() && VanillaX::getInstance()->getConfig()->get("gamerule-remove-cache", true)){
            foreach(Server::getInstance()->getWorldManager() as $world){
                $provider = $world->getProvider();

                if($provider instanceof BaseWorldProvider){
                    $data = $provider->getWorldData();

                    if($data instanceof BedrockWorldData){
                        /** @var CompoundTag $nbt */
                        $nbt = $data->getCompoundTag()->getTag("GameRules");

                        foreach($this->gameRules as $rule){
                            if(($tag = $nbt->getTag($rule->getName())) !== null){
                                $tag = $tag->getValue();

                                if($rule->getType() === GameRule::TYPE_BOOL && $tag instanceof ByteTag){
                                    $nbt->removeTag($rule->getName());
                                }elseif($rule->getType() === GameRule::TYPE_INT && $tag instanceof IntTag){
                                    $nbt->removeTag($rule->getName());
                                }
                                $data->save();
                            }
                        }
                    }
                }
            }
        }
    }

    public function startup(): void{
        foreach($this->gameRules as $rule){
            foreach(Server::getInstance()->getWorldManager() as $world){
                $rule->handleValue($this->getValue($rule->getName(), $world), $world);
            }
        }
    }

    private function register(GameRule $rule): void{
        $this->gameRules[strtolower($rule->getName())] = $rule;
    }


    /**
     * @param World $World
     * @param GameRule $rule
     * @param int|bool $value
     * @param bool $force
     */
    public function set(World $World, GameRule $rule, $value, bool $force = false): void{
        if(!$force && !$this->isEnabled()){
            return;
        }
        $provider = $World->getProvider();

        if($provider instanceof BaseWorldProvider){
            $data = $provider->getWorldData();

            if($data instanceof BedrockWorldData){
                /** @var CompoundTag $nbt */
                $nbt = $data->getCompoundTag()->getTag("GameRules");

                if($nbt->getTag($rule->getName())) $nbt->removeTag($rule->getName());
                if(is_bool($value)){
                    $nbt->setByte($rule->getName(), $value);
                }else{
                    $nbt->setInt($rule->getName(), $value);
                }
                $data->save();
            }
        }
    }

    public function sendChanges(Player $player, World $World = null): void{
        if(!$this->isEnabled()){
            return;
        }
        if($World === null){
            $World = $player->getWorld();
        }
        $provider = $World->getProvider();

        if($provider instanceof BaseWorldProvider){
            $data = $provider->getWorldData();

            if($data instanceof BedrockWorldData){
                /** @var CompoundTag $nbt */
                $nbt = $data->getCompoundTag()->getTag("GameRules");

                foreach($nbt->getValue() as $key => $tag){
                    if($tag instanceof ByteTag){
                        $value = new BoolGameRule(boolval($tag->getValue()), false);
                    }else{
                        $value = new IntGameRule(intval($tag->getValue()), false);
                    }
                    $pk = new GameRulesChangedPacket();
                    $pk->gameRules = [$key => $value];
                    $player->getNetworkSession()->sendDataPacket($pk);
                }
            }
        }
    }

    /**
     * @param string $name
     * @param World $World
     * @param bool $stringify
     * @return bool|int|string|null
     */
    public function getValue(string $name, World $World, bool $stringify = false){
        $name = strtolower($name);
        $rule = $this->gameRules[$name] ?? null;
        $provider = $World->getProvider();

        if($rule !== null && !$this->isEnabled()) return $rule->getDefaultValue();
        if($rule !== null && $provider instanceof BaseWorldProvider){
            $data = $provider->getWorldData();

            if($data instanceof BedrockWorldData){
                /** @var CompoundTag $nbt */
                $nbt = $data->getCompoundTag()->getTag("GameRules");
                $tag = $nbt->getValue()[$rule->getName()] ?? null;

                if($tag instanceof Tag){
                    if($stringify && $tag instanceof ByteTag){
                        return boolval($tag->getValue()) ? "true" : "false";
                    }
                    return $tag instanceof ByteTag ? boolval($tag->getValue()) : intval($tag->getValue());
                }
            }
        }
        if($stringify && is_bool($rule->getDefaultValue())){
            return $rule->getDefaultValue() ? "true" : "false";
        }
        return $rule === null ? null : $rule->getDefaultValue();
    }

    /**
     * @return GameRule[]
     */
    public function getAll(): array{
        return $this->gameRules;
    }

    public function getByName(string $name): ?GameRule{
        return $this->gameRules[strtolower($name)] ?? null;
    }

    public function isEnabled(): bool{
        return boolval(VanillaX::getInstance()->getConfig()->getNested("features.gamerule", true));
    }
}