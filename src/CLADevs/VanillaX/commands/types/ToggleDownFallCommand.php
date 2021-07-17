<?php

namespace CLADevs\VanillaX\commands\types;

use CLADevs\VanillaX\commands\Command;
use CLADevs\VanillaX\commands\utils\CommandArgs;
use CLADevs\VanillaX\world\weather\WeatherManager;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\Server;

class ToggleDownFallCommand extends Command{

    public function __construct(){
        parent::__construct("toggledownfall", "Toggles the weather.");
        $this->setPermission("toggledownfall.command");
        $this->commandArg = new CommandArgs(CommandArgs::FLAG_NORMAL, PlayerPermissions::MEMBER);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        foreach(Server::getInstance()->getWorldManager()->getWorlds() as $world){
            if(($weather = WeatherManager::getInstance()->getWeather($world)) !== null){
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