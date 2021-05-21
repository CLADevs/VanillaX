<?php

namespace CLADevs\VanillaX\commands;

use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class CommandTargetSelector{

    /**
     * @param Player $player
     * @param string $data
     * @param bool $message
     * @param bool $returnArray
     * @param bool $playerOnly
     * @return Entity[]|Player|Player[]|null
     */
    public static function getFromString(Player $player, string $data, bool $message = true, bool $returnArray = true, bool $playerOnly = false){
        switch($data){
            case "@a":
                return self::getAllPlayers();
            case "@e":
                if($playerOnly){
                    if($message) $player->sendMessage(TextFormat::RED . "Selector must be player-type");
                    return null;
                }
                return self::getAllEntities($player->getLevel());
            case "@r":
                $p = self::getRandomPlayer();
                return $p === null ? null : ($returnArray ? [$p] : $p);
            case "@s":
                return $returnArray? [$player] : $player;
            default:
                if($p = Server::getInstance()->getPlayer($data)){
                    return $p;
                }
                if($message) $player->sendMessage(TextFormat::RED . "No targets matched selector");
                break;
        }
        return null;
    }

    /**
     * @return null|Player[]
     */
    public static function getAllPlayers(): ?array{
        $players = Server::getInstance()->getOnlinePlayers();
        return count($players) < 1 ? null : $players;
    }

    /**
     * @param null|Level $level
     * @return null|Entity[]
     */
    public static function getAllEntities(?Level $level = null): ?array{
        if($level === null){
            $level = Server::getInstance()->getLevels();
        }else{
            $level = [$level];
        }
        $entities = [];
        foreach($level as $lvl){
            $entities = array_merge($entities, $lvl->getEntities());
        }
        return count($entities) < 1 ? null : $entities;
    }

    public static function getRandomPlayer(): ?Player{
        $players = self::getAllPlayers();

        if(count($players) >= 1){
            return $players[array_rand($players)];
        }
        return null;
    }
}