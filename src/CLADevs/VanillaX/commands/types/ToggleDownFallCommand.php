<?php

namespace CLADevs\VanillaX\commands\types;

use CLADevs\VanillaX\commands\Command;
use CLADevs\VanillaX\commands\utils\CommandArgs;
use CLADevs\VanillaX\VanillaX;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\Server;

class ToggleDownFallCommand extends Command{

    public function __construct(){
        parent::__construct("toggledownfall", "Toggles the weather.");
        $this->commandArg = new CommandArgs(CommandArgs::FLAG_NORMAL, PlayerPermissions::MEMBER);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        foreach(Server::getInstance()->getLevels() as $level){
            if(($weather = VanillaX::getInstance()->getWeatherManager()->getWeather($level)) !== null){
                if($weather->isRaining()){
                    $weather->stopStorm();
                }else{
                    $weather->startStorm();
                }
            }
            }
        $sender->sendMessage("Toggled downfall");
    }
}