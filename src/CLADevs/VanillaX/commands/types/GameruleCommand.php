<?php

namespace CLADevs\VanillaX\commands\types;

use CLADevs\VanillaX\commands\Command;
use CLADevs\VanillaX\commands\utils\CommandArgs;
use CLADevs\VanillaX\commands\utils\CommandOverload;
use CLADevs\VanillaX\configuration\Setting;
use CLADevs\VanillaX\world\gamerule\GameRule;
use CLADevs\VanillaX\world\gamerule\GameRuleManager;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\types\IntGameRule;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\player\Player;
use pocketmine\Server;

class GameruleCommand extends Command{

    public function __construct(){
        parent::__construct("gamerule", "Set or queries a game rule value.");
        $this->setPermission("gamerule.command");
        $intGameRules = [];
        $boolGameRules = [];
        foreach(GameRuleManager::getInstance()->getAll() as $rule){
            if($rule->getType() === GameRule::TYPE_INT){
                $intGameRules[] = strtolower($rule->getName());
            }else{
                $boolGameRules[] = strtolower($rule->getName());
            }
        }
        $this->commandArg = new CommandArgs(PlayerPermissions::MEMBER);

        $overload = new CommandOverload();
        $overload->addEnum("rule", new CommandEnum("BoolGameRule", $boolGameRules), false);
        $overload->addEnum("value", new CommandEnum("Boolean", ["true", "false"]));
        $this->commandArg->addOverload($overload);

        $overload = new CommandOverload();
        $overload->addEnum("rule", new CommandEnum("IntGameRule", $intGameRules), false);
        $overload->addInt("value");
        $this->commandArg->addOverload($overload);
    }

    public function canRegister(): bool{
        return Setting::getInstance()->isGameRuleEnabled();
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
