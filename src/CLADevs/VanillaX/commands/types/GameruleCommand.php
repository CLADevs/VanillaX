<?php

namespace CLADevs\VanillaX\commands\types;

use CLADevs\VanillaX\commands\Command;
use CLADevs\VanillaX\commands\CommandArgs;
use CLADevs\VanillaX\network\gamerules\GameRule;
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
        $values = [];
        foreach(GameRule::$gameRules as $key => $rule){
            $values[] = $key . " = " . GameRule::getGameRuleValue($rule->getName(), $sender->getLevel(), true);
        }
        if(!isset($args[0])){
            $sender->sendMessage(implode(", ", $values));
            return;
        }
        $gameRule = GameRule::$gameRules[strtolower($args[0])] ?? null;
        if($gameRule === null){
            $errorArg = $args;
            array_shift($errorArg);
            $this->sendSyntaxError($sender, $args[0], "/$commandLabel", $args[0], $errorArg);
            return;
        }
        if(!isset($args[1])){
            $sender->sendMessage(strtolower($gameRule->getName()) . " = " . GameRule::getGameRuleValue($gameRule->getName(), $sender->getLevel(), true));
            return;
        }
        $pk = new GameRulesChangedPacket();
        $value = $args[1];

        if($gameRule->getType() === GameRule::TYPE_INT){
            if(!is_numeric($value)){
                $this->sendSyntaxError($sender, $value, $args[0], $value);
                return;
            }
            $value = intval($value);
        }else{
            if(!in_array(strtolower($value), ["true", "false"])){
                $this->sendSyntaxError($sender, $value, $args[0], $value);
                return;
            }
            $value = strtolower($value) === "true";
        }
        $pk->gameRules = [$gameRule->getName() => [is_bool($value) ? 1 : 0, $value]];
        foreach($sender->getLevel()->getPlayers() as $player){
            $player->dataPacket($pk);
        }
        GameRule::setGameRule($sender->getLevel(), $gameRule, $value);
        $gameRule->handleValue($value, $sender->getLevel());
        $sender->sendMessage("Game rule " . strtolower($gameRule->getName()) . " has been updated to " . $value);
    }
}