<?php

namespace CLADevs\VanillaX\session;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

class SessionManager{
    use SingletonTrait;

    /** @var Session[] */
    private array $sessions = [];

    public function __construct(){
        self::setInstance($this);
    }

    /**
     * @param string|Player $player
     * @return bool
     */
    public function has($player): bool{
        return isset($this->sessions[$player instanceof Player ? $player->getName() : $player]);
    }

    /**
     * @param Player|string $player
     * @return null|Session
     */
    public function get($player): ?Session{
        if($player instanceof Player) $this->add($player);
        return $this->sessions[$player instanceof Player ? $player->getName() : $player] ?? null;
    }

    public function add(Player $player): Session{
        if(!$this->has($player)){
            $this->sessions[$player->getName()] = new Session($player);
        }
        return $this->sessions[$player->getName()];
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