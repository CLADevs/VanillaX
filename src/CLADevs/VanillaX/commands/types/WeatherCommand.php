<?php

namespace CLADevs\VanillaX\commands\types;

use CLADevs\VanillaX\commands\Command;
use CLADevs\VanillaX\commands\utils\CommandArgs;
use CLADevs\VanillaX\VanillaX;
use CLADevs\VanillaX\world\weather\WeatherManager;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class WeatherCommand extends Command{

    public function __construct(){
        parent::__construct("weather", "Sets the weather.");
        $this->setPermission("weather.command");
        $this->commandArg = new CommandArgs(CommandArgs::FLAG_NORMAL, PlayerPermissions::MEMBER);
        /** First Column */
        $this->commandArg->addParameter(0, "clear", AvailableCommandsPacket::ARG_FLAG_ENUM | 0x69 | 0x4, false, "rain: thunder", ["clear", "rain", "thunder"]);
        $this->commandArg->addParameter(0, "duration", AvailableCommandsPacket::ARG_TYPE_INT);
        /** Second Column */
        $this->commandArg->addParameter(1, "query", AvailableCommandsPacket::ARG_FLAG_ENUM | 0x68 | 0x6, false, "WeatherQuery", ["query"]);
    }

    public function canRegister(): bool{
        return boolval(VanillaX::getInstance()->getConfig()->getNested("features.weather", true));
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if(!$this->testPermission($sender)) return;
        if(!isset($args[0])){
            $this->sendSyntaxError($sender, "", "/$commandLabel");
            return;
        }
        $duration = 6000;
        $weathers = [];
        foreach(Server::getInstance()->getWorldManager()->getWorlds() as $world){
            if(($weather = WeatherManager::getInstance()->getWeather($world)) !== null){
                $weathers[] = $weather;
            }
        }

        if(isset($args[1]) && is_numeric($args[1])){
            $duration = intval($args[1]);
        }
        switch($type = strtolower($args[0])){
            case "clear":
                foreach($weathers as $weather) $weather->stopStorm();
                $sender->sendMessage("Changing to clear weather");
                break;
            case "query":
                if(!$sender instanceof Player){
                    $sender->sendMessage(TextFormat::RED . "This command is only available in game.");
                    return;
                }
                $state = "clear";
                $weather = WeatherManager::getInstance()->getWeather($sender->getWorld());
                if($weather->isRaining()){
                    if($weather->isThundering()){
                        $state = "thunder";
                    }else{
                        $state = "rain";
                    }
                }
                $sender->sendMessage("Weather state is: " . $state);
                return;
            case "rain":
                foreach($weathers as $weather) $weather->startStorm(false, $duration);
                $sender->sendMessage("Changing to rainy weather");
                return;
            case "thunder":
                foreach($weathers as $weather) $weather->startStorm(true, $duration);
                $sender->sendMessage("Changing to rain and thunder");
                return;
            default:
                $this->sendSyntaxError($sender, $type, "/$commandLabel", $type);
        }
    }
}