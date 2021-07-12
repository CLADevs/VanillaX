<?php

namespace CLADevs\VanillaX\commands\utils;

use CLADevs\VanillaX\commands\Command;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class CommandHelper{

    public static function getPosition(CommandSender $sender, Command $command, int $startIndex, array $args): ?Vector3{
        if(isset($args[$startIndex])){
            if(!isset($args[$startIndex + 2])){
                $command->sendSyntaxError($sender, "", implode(" ", $args));
                return null;
            }
            $x = $args[$startIndex];
            $y = $args[$startIndex + 1];
            $z = $args[$startIndex + 2];

            foreach([$x, $y, $z] as $coord){
                if(!is_numeric($coord)){
                    if($sender instanceof Player && $coord === "~"){
                        $x = $sender->getPosition()->x;
                        $y = $sender->getPosition()->y;
                        $z = $sender->getPosition()->z;
                        continue;
                    }
                    $command->sendSyntaxError($sender, $coord, implode(" ", $args), $coord);
                    return null;
                }
            }
            return new Vector3(floatval($x), floatval($y), floatval($z));
        }
        return null;
    }
}