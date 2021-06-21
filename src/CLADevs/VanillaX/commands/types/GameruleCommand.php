<?php

namespace CLADevs\VanillaX\commands\types;

use CLADevs\VanillaX\commands\Command;
use CLADevs\VanillaX\commands\utils\CommandArgs;
use CLADevs\VanillaX\network\gamerules\GameRule;
use CLADevs\VanillaX\VanillaX;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\Player;
use pocketmine\Server;

class GameruleCommand extends Command{

    public function __construct(){
        parent::__construct("gamerule", "Set or queries a game rule value.");
        $this->setPermission("gamerule.command");
        $this->commandArg = new CommandArgs(CommandArgs::FLAG_NORMAL, PlayerPermissions::MEMBER);
        /** First Column */
        $key = $this->commandArg->addParameter(0, "rule", AvailableCommandsPacket::ARG_FLAG_ENUM | AvailableCommandsPacket::ARG_TYPE_STRING, false);
        $this->commandArg->setEnum(0, $key, "BoolGameRule", ["commandblockoutput", "dodaylightcycle", "doentitydrops", "dofiretick", "domobloot", "domobspawning", "dotiledrops", "doweathercycle", "drowningdamage", "falldamage", "firedamage", "keepinventory", "mobgriefing", "pvp", "showcoordinates", "naturalregeneration", "tntexplodes", "sendcommandfeedback", "doinsomnia", "commandblocksenabled", "doimmediaterespawn", "showdeathmessages", "showtags"]);

        $this->commandArg->addParameter(0, "value", AvailableCommandsPacket::ARG_FLAG_ENUM | AvailableCommandsPacket::ARG_TYPE_WILDCARD_INT, true, "Boolean", ["true", "false"]);

        /** Second Column */
        $this->commandArg->addParameter(1, "rule", AvailableCommandsPacket::ARG_FLAG_ENUM | 0x21, false, "IntGameRule", ["maxcommandchainlength", "randomtickspeed", "functioncommandlimit", "spawnradius"]);
        $this->commandArg->addParameter(1, "value", AvailableCommandsPacket::ARG_TYPE_INT);
    }

    public function canRegister(): bool{
        return boolval(VanillaX::getInstance()->getConfig()->getNested("features.gamerule", true));
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if(!$this->testPermission($sender)) return;
        if(!isset($args[0]) || !isset($args[1])){
            $this->sendSyntaxError($sender, "", "/$commandLabel");
            return;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage(TextFormat::RED . "This command is only available in game.");
            return;
        }
        if(!isset($args[0])){
            $values = [];
            foreach(GameRule::$gameRules as $key => $rule){
                $values[] = $key . " = " . GameRule::getGameRuleValue($rule->getName(), $sender->getLevel(), true);
            }
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
        $pk->gameRules = [$gameRule->getName() => [is_bool($value) ? 1 : 0, $value, false]];
        foreach(Server::getInstance()->getLevels() as $level){
            foreach($level->getPlayers() as $player){
                $player->dataPacket($pk);
            }
            GameRule::setGameRule($level, $gameRule, $value);
            $gameRule->handleValue($value, $level);
        }
        $sender->sendMessage("Game rule " . strtolower($gameRule->getName()) . " has been updated to " . $args[1]);
    }
}
