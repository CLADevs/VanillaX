<?php

namespace CLADevs\VanillaX\commands\types;

use CLADevs\VanillaX\commands\Command;
use CLADevs\VanillaX\commands\utils\CommandArgs;
use CLADevs\VanillaX\VanillaX;
use CLADevs\VanillaX\world\gamerule\GameRule;
use CLADevs\VanillaX\world\gamerule\GameRuleManager;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocketmine\network\mcpe\protocol\types\IntGameRule;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\player\Player;
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
        if((!isset($args[0]) || !isset($args[1])) && !$sender instanceof Player){
            $this->sendSyntaxError($sender, "", "/$commandLabel");
            return;
        }
        $instance = GameRuleManager::getInstance();
        if(!isset($args[0]) && $sender instanceof Player){
            $values = [];
            foreach($instance->getAll() as $key => $rule){
                $values[] = $key . " = " . $instance->getValue($rule->getName(), $sender->getWorld(), true);
            }
            $sender->sendMessage(implode(", ", $values));
            return;
        }
        $gameRule = $instance->getByName($args[0]);
        if($gameRule === null){
            $errorArg = $args;
            array_shift($errorArg);
            $this->sendSyntaxError($sender, $args[0], "/$commandLabel", $args[0], $errorArg);
            return;
        }
        if(!isset($args[1]) && $sender instanceof Player){
            $sender->sendMessage(strtolower($gameRule->getName()) . " = " . $instance->getValue($gameRule->getName(), $sender->getWorld(), true));
            return;
        }
        $pk = new GameRulesChangedPacket();
        $value = $args[1];
        $gameruleClass = null;

        if($gameRule->getType() === GameRule::TYPE_INT){
            if(!is_numeric($value)){
                $this->sendSyntaxError($sender, $value, $args[0], $value);
                return;
            }
            $value = intval($value);
            $gameruleClass = new IntGameRule($value, false);
        }else{
            if(!in_array(strtolower($value), ["true", "false"])){
                $this->sendSyntaxError($sender, $value, $args[0], $value);
                return;
            }
            $value = strtolower($value) === "true";
            $gameruleClass = new BoolGameRule($value, false);
        }
        $pk->gameRules = [$gameRule->getName() => $gameruleClass];
        foreach(Server::getInstance()->getWorldManager()->getWorlds() as $world){
            foreach($world->getPlayers() as $player){
                $player->getNetworkSession()->sendDataPacket($pk);
            }
            $instance->set($world, $gameRule, $value);
            $gameRule->handleValue($value, $world);
        }
        $sender->sendMessage("Game rule " . strtolower($gameRule->getName()) . " has been updated to " . $args[1]);
    }
}
