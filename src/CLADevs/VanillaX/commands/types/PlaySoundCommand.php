<?php

namespace CLADevs\VanillaX\commands\types;

use CLADevs\VanillaX\commands\Command;
use CLADevs\VanillaX\commands\utils\CommandArgs;
use CLADevs\VanillaX\commands\utils\CommandHelper;
use CLADevs\VanillaX\commands\utils\CommandOverload;
use CLADevs\VanillaX\commands\utils\CommandTargetSelector;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\player\Player;

class PlaySoundCommand extends Command{

    public function __construct(){
        parent::__construct("playsound", "Plays a sound.");
        $this->setPermission("playsound.command");
        $this->commandArg = new CommandArgs(PlayerPermissions::MEMBER);

        $overload = new CommandOverload();
        $overload->addString("sound", false);
        $overload->addTarget("player");
        $overload->addPosition("position");
        $overload->addFloat("volume");
        $overload->addFloat("pitch");
        $overload->addFloat("minimumVolume");
        $this->commandArg->addOverload($overload);

    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if(!isset($args[0]) || (!isset($args[1]) && $sender instanceof ConsoleCommandSender)){
            $this->sendSyntaxError($sender, "", "/$commandLabel");
            return;
        }
        $sound = $args[0];
        $players = [$sender];
        $position = null;
        $volume = 1.0;
        $pitch = 1.0;
        $minimumVolume = 1.0;

        if(isset($args[1])){
            if(!$players = CommandTargetSelector::getFromString($sender, $args[1], true, true, true)){
                return;
            }
            if(isset($args[2])){
                if(!$position = CommandHelper::getPosition($sender, $this, 2, $args)){
                    return;
                }
            }
            if(isset($args[5])){
                if(!is_numeric($args[5])){
                    $this->sendSyntaxError($sender, $args[5], implode(" ", $args), $args[5]);
                    return;
                }
                $volume = floatval($args[5]);

                if(isset($args[6])){
                    if(!is_numeric($args[6])){
                        $this->sendSyntaxError($sender, $args[6], implode(" ", $args), $args[6]);
                        return;
                    }
                    $pitch = floatval($args[6]);

                    if(isset($args[7])){
                        if(!is_numeric($args[7])){
                            $this->sendSyntaxError($sender, $args[7], implode(" ", $args), $args[6]);
                            return;
                        }
                        $minimumVolume = floatval($args[7]);
                    }
                }
            }
        }
        foreach($players as $player){
            if($player instanceof Player){
                $pos = $position === null ? $player->getPosition() : $position;
                $pk = new PlaySoundPacket();
                $pk->soundName = $sound;
                $pk->volume = $volume > $minimumVolume ? $minimumVolume : $volume;
                $pk->pitch = $pitch;
                $pk->x = $pos->x;
                $pk->y = $pos->y;
                $pk->z = $pos->z;
                $player->getNetworkSession()->sendDataPacket($pk);
                $sender->sendMessage("Played sound '$sound' to " . $player->getName());
            }
        }
    }
}