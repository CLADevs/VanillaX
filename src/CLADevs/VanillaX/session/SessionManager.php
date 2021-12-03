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

    public function has(Player|string $player): bool{
        return isset($this->sessions[$player instanceof Player ? $player->getName() : $player]);
    }

    public function get(Player|string $player): ?Session{
        if($player instanceof Player) $this->add($player);
        return $this->sessions[$player instanceof Player ? $player->getName() : $player] ?? null;
    }

    public function add(Player $player): Session{
        if(!$this->has($player)){
            $this->sessions[$player->getName()] = new Session($player);
        }
        return $this->sessions[$player->getName()];
    }

    public function remove(Player|string $player): void{
        if($this->has($player)){
            unset($this->sessions[$player instanceof Player ? $player->getName() : $player]);
        }
    }
}