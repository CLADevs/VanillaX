<?php

namespace CLADevs\VanillaX\commands\utils;

use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;

class CommandTargetSelector{

    /**
     * @param CommandSender $player
     * @param string $data
     * @param bool $message
     * @param bool $returnArray
     * @param bool $playerOnly
     * @return Entity[]|Player|Player[]|CommandSender[]|CommandSender|null
     */
    public static function getFromString(CommandSender $player, string $data, bool $message = true, bool $returnArray = true, bool $playerOnly = false){
        $noSelectorMessage = TextFormat::RED . "No targets matched selector";
        switch($data){
            case "@a":
                $players = self::getAllPlayers();

                if($players === null || (is_array($players && count($players) < 1))){
                    if($message) $player->sendMessage($noSelectorMessage);
                    return null;
                }
                return $players;
            case "@e":
                if($playerOnly){
                    if($message) $player->sendMessage(TextFormat::RED . "Selector must be player-type");
                    return null;
                }
                if(!$player instanceof Player){
                    if($message) $player->sendMessage(TextFormat::RED . "You must be in a world");
                    return null;
                }
                $entities = self::getAllEntities($player->getWorld());

                if($entities === null || (is_array($entities && count($entities) < 1))){
                    if($message) $player->sendMessage($noSelectorMessage);
                    return null;
                }
                return $entities;
            case "@r":
                $p = self::getRandomPlayer();
                $chosen = $p === null ? null : ($returnArray ? [$p] : $p);

                if($chosen === null || (is_array($chosen && count($chosen) < 1))){
                    if($message) $player->sendMessage($noSelectorMessage);
                    return null;
                }
                return $chosen;
            case "@s":
                return $returnArray? [$player] : $player;
            default:
                if($p = Server::getInstance()->getPlayerByPrefix($data)){
                    return [$p];
                }
                if($message) $player->sendMessage($noSelectorMessage);
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
     * @param null|World $world
     * @return null|Entity[]
     */
    public static function getAllEntities(?World $world = null): ?array{
        if($world === null){
            $world = Server::getInstance()->getWorldManager()->getWorlds();
        }else{
            $world = [$world];
        }
        $entities = [];
        foreach($world as $lvl){
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