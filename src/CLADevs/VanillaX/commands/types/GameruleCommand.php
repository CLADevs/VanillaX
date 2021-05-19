<?php

namespace CLADevs\VanillaX\commands\types;

use CLADevs\VanillaX\commands\Command;
use CLADevs\VanillaX\network\GameRule;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class GameruleCommand extends Command{

    public function __construct(){
        parent::__construct("gamerule", "Set or queries a game rule value.");
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