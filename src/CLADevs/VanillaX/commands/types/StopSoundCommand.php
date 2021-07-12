<?php

namespace CLADevs\VanillaX\commands\types;

use CLADevs\VanillaX\commands\Command;
use CLADevs\VanillaX\commands\utils\CommandArgs;
use CLADevs\VanillaX\commands\utils\CommandTargetSelector;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\StopSoundPacket;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\player\Player;

class StopSoundCommand extends Command{

    public function __construct(){
        parent::__construct("stopsound", "Stops a sound.");
        $this->setPermission("stopsound.command");
        $this->commandArg = new CommandArgs(CommandArgs::FLAG_NORMAL, PlayerPermissions::MEMBER);
        $this->commandArg->addParameter(0, "player", AvailableCommandsPacket::ARG_TYPE_TARGET);
        $this->commandArg->addParameter(0, "sound", AvailableCommandsPacket::ARG_TYPE_STRING, false);

    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if(!isset($args[1])){
            $this->sendSyntaxError($sender, "", "/$commandLabel");
            return;
        }
        if(!$players = CommandTargetSelector::getFromString($sender, $args[0], true, true, true)){
            return;
        }
        $sound = $args[1];

        foreach($players as $player){
            if($player instanceof Player){
                $pk = new StopSoundPacket();
                $pk->soundName = $sound;
                $pk->stopAll = false;
                $player->getNetworkSession()->sendDataPacket($pk);
                $sender->sendMessage("Stopped sound '$sound' for " . $player->getName());
            }
        }
    }
}