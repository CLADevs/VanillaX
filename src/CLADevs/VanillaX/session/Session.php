<?php

namespace CLADevs\VanillaX\session;

use pocketmine\Player;

class Session{

    private Player $player;

    private bool $gliding = false;
    private ?int $startGlideTime = null;
    private ?int $endGlideTime = null;

    public function __construct(Player $player){
        $this->player = $player;
    }

    public function isGliding(): bool{
        return $this->gliding;
    }

    public function setGliding(bool $value = true): void{
        $this->gliding = $value;
        if($value){
            $this->startGlideTime = time();
        }else{
            $this->endGlideTime = time();
        }
    }

    public function getStartGlideTime(): ?int{
        return $this->startGlideTime;
    }

    public function getEndGlideTime(): ?int{
        return $this->endGlideTime;
    }
}