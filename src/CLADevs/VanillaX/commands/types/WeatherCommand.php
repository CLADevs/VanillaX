<?php

namespace CLADevs\VanillaX\commands\types;

use CLADevs\VanillaX\commands\Command;
use CLADevs\VanillaX\commands\utils\CommandArgs;
use CLADevs\VanillaX\commands\utils\CommandOverload;
use CLADevs\VanillaX\configuration\Setting;
use CLADevs\VanillaX\world\weather\WeatherManager;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class WeatherCommand extends Command{

    public function __construct(){
        parent::__construct("weather", "Sets the weather.");
        $this->setPermission("weather.command");
        $this->commandArg = new CommandArgs(PlayerPermissions::MEMBER);

        $overload = new CommandOverload();
        $overload->addEnum("clear", new CommandEnum("rain: thunder", ["clear", "rain", "thunder"]), false);
        $overload->addInt("duration");
        $this->commandArg->addOverload($overload);

        $overload = new CommandOverload();
        $overload->addEnum("query", new CommandEnum("WeatherQuery", ["query"]), false);
        $this->commandArg->addOverload($overload);
    }

    public function canRegister(): bool{
        return Setting::getInstance()->isWeatherEnabled();
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