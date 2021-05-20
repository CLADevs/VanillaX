<?php

namespace CLADevs\VanillaX\commands\types;

use CLADevs\VanillaX\commands\Command;
use CLADevs\VanillaX\commands\CommandArgs;
use CLADevs\VanillaX\entities\object\LightningBoltEntity;
use CLADevs\VanillaX\VanillaX;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class WeatherCommand extends Command{

    public function __construct(){
        parent::__construct("weather", "Sets the weather.");
        $this->commandArg = new CommandArgs(0, 1);
        $key = $this->commandArg->addParameter(0, "clear", 3145837, false);
        $this->commandArg->setEnum(0, $key, "rain: thunder", ["clear", "rain", "thunder"]);

        $key = $this->commandArg->addParameter(0, "duration", 1048577);
        $this->commandArg->setEnum(0, $key, "int");

        $key = $this->commandArg->addParameter(1, "query", 3145838, false);
        $this->commandArg->setEnum(1, $key, "WeatherQuery", ["query"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if(!$sender instanceof Player){
            $sender->sendMessage(TextFormat::RED . "This command is only available in game.");
            return;
        }
        if(!isset($args[0])){
            $this->sendSyntaxError($sender, "", "/$commandLabel");
            return;
        }
        $duration = 6000;
        $weather = VanillaX::getInstance()->getWeatherManager()->getWeather($sender->getLevel());

        if(isset($args[1]) && is_numeric($args[1])){
            $duration = intval($args[1]);
        }
        switch($type = strtolower($args[0])){
            case "clear":
                $weather->stopStorm();
                $sender->sendMessage("Changing to clear weather");
                break;
            case "query":
                $state = "clear";
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
                $weather->startStorm(false, $duration);
                $sender->sendMessage("Changing to rainy weather");
                return;
            case "thunder":
                $weather->startStorm(true, $duration);
                $sender->sendMessage("Changing to rain and thunder");
                return;
            default:
                $this->sendSyntaxError($sender, $type, "/$commandLabel", $type);
                return;
        };
    }
}