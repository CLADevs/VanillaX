<?php

namespace CLADevs\VanillaX\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class GameruleCommand extends Command{

    //TODO freezeDamage
    const GAME_RULES = [
        "commandBlocksEnabled", "commandBlockOutput", "doDaylightCycle", "doEntityDrops",
        "doFireTick", "doInsomnia", "doImmediateRespawn", "doMobLoot", "doMobSpawning",
        "doTileDrops", "doWeatherCycle", "drowningDamage", "fallDamage", "fireDamage",
        "keepInventory", "maxCommandChainLength", "mobGriefing", "naturalRegeneration",
        "pvp", "randomTickSpeed", "sendCommandFeedback", "showCoordinates", "showDeathMessages",
        "spawnRadius", "tntExplodes", "showTags"
    ];

    public function __construct(){
        parent::__construct("gamerule", "Set or queries a game rule value.");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        //TODO save it with level
        if(!isset($args[1])){
            $sender->sendMessage("Usage: $commandLabel <rule> <value>");
            return;
        }
        if(!in_array($args[0], self::GAME_RULES)){
            $sender->sendMessage(TextFormat::RED . "Unknown game rule.");
            return;
        }
        $pk = new GameRulesChangedPacket();
        $value = $args[1];
        $rule = self::GAME_RULES[array_search($args[0], self::GAME_RULES)];
        if(in_array($rule, ["maxCommandChainLength", "randomTickSpeed", "spawnRadius"])){
            if(!is_numeric($value) && $value >= 0){
                $sender->sendMessage(TextFormat::RED . "Value must be a number.");
                return;
            }
            $value = intval($value);
            $pk->gameRules = [$rule => [0, $value]];
        }else{
            $value = boolval($value);
            if($value === true || $value === false){
                $sender->sendMessage(TextFormat::RED . "Value must be true or false.");
                return;
            }
            $pk->gameRules = [$rule => [1, $value]];
        }
        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            $player->dataPacket($pk);
        }
        $sender->sendMessage(TextFormat::GREEN . "Successfully updated " . $rule . " value to " . $value);
    }
}