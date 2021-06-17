<?php

namespace CLADevs\VanillaX\session;

use pocketmine\Player;

class SessionManager{

    /** @var Session[] */
    private array $sessions = [];

    /**
     * @param string|Player $player
     * @return bool
     */
    public function has($player): bool{
        return isset($this->sessions[$player instanceof Player ? $player->getName() : $player]);
    }

    /**
     * @param Player|string $player
     * @return Session
     */
    public function get($player): Session{
        if($player instanceof Player) $this->add($player);
        return $this->sessions[$player instanceof Player ? $player->getName() : $player];
    }

    public function add(Player $player): void{
        if(!$this->has($player)){
            $this->sessions[$player->getName()] = new Session($player);
        }
    }

    /**
     * @param string|Player $player
     */
    public function remove($player): void{
        if($this->has($player)){
            unset($this->sessions[$player instanceof Player ? $player->getName() : $player]);
        }
    }
}