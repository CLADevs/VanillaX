<?php

namespace CLADevs\VanillaX\commands\types;

use CLADevs\VanillaX\commands\Command;
use CLADevs\VanillaX\commands\CommandArgs;
use CLADevs\VanillaX\network\GameRule;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class GameruleCommand extends Command{

    public function __construct(){
        parent::__construct("gamerule", "Set or queries a game rule value.");
        $this->commandArg = new CommandArgs(64, 1);
        $key = $this->commandArg->addParameter(0, "rule", 3145760, false);
        $this->commandArg->setEnum(0, $key, "BoolGameRule", ["commandblockoutput", "dodaylightcycle", "doentitydrops", "dofiretick", "domobloot", "domobspawning", "dotiledrops", "doweathercycle", "drowningdamage", "falldamage", "firedamage", "keepinventory", "mobgriefing", "pvp", "showcoordinates", "naturalregeneration", "tntexplodes", "sendcommandfeedback", "doinsomnia", "commandblocksenabled", "doimmediaterespawn", "showdeathmessages", "showtags"]);

        $key = $this->commandArg->addParameter(0, "value", 3145733);
        $this->commandArg->setEnum(0, $key, "Boolean", ["true", "false"]);

        $key = $this->commandArg->addParameter(1, "rule", 3145761, false);
        $this->commandArg->setEnum(1, $key, "IntGameRule", ["maxcommandchainlength", "randomtickspeed", "functioncommandlimit", "spawnradius"]);

        $key = $this->commandArg->addParameter(1, "value", 1048577);
        $this->commandArg->setEnum(1, $key, "int");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if(!$sender instanceof Player){
            $sender->sendMessage(TextFormat::RED . "This command is only available in game.");
            return;
        }
        if(!isset($args[0])){
            $sender->sendMessage("Usage: /$commandLabel <rule> <value>");
            return;
        }
        $gameRule = GameRule::$gameRules[strtolower($args[0])] ?? null;
        if($gameRule === null){
            $sender->sendMessage(TextFormat::RED . "Unknown game rule.");
            return;
        }
        if(!isset($args[1])){
            $value = GameRule::getGameRuleValue($gameRule->getName(), $sender->getLevel());
            $sender->sendMessage(TextFormat::GREEN . $gameRule->getName() . " is currently: " . is_bool($value) ? ($value ? "true" : "false") : $value);
            return;
        }
        $pk = new GameRulesChangedPacket();
        $value = $args[1];

        if($gameRule->getType() === GameRule::TYPE_INT){
            if(!is_numeric($value) && $value >= 0){
                $sender->sendMessage(TextFormat::RED . "Value must be a number.");
                return;
            }
            $value = intval($value);
        }else{
            if(!in_array(strtolower($value), ["true", "false", "0", "1"])){
                $sender->sendMessage(TextFormat::RED . "Value must be true or false.");
                return;
            }
            if(in_array(strtolower($value), ["true", "1"])){
                $value = true;
            }else{
                $value = false;
            }
        }
        $pk->gameRules = [$gameRule->getName() => [is_bool($value) ? 1 : 0, $value]];
        foreach($sender->getLevel()->getPlayers() as $player){
            $player->dataPacket($pk);
        }
        GameRule::setGameRule($sender->getLevel(), $gameRule, $value);
        $gameRule->handleValue($value, $sender->getLevel());
        $sender->sendMessage(TextFormat::GREEN . "Successfully updated " . $gameRule->getName() . " value to " . $args[1]);
    }
}