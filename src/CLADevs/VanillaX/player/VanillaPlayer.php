<?php

namespace CLADevs\VanillaX\player;

use pocketmine\player\Player;

class VanillaPlayer extends Player{

    public function setXpSeed(int $xpSeed): void{
        $this->xpSeed = $xpSeed;
    }

    public function getXpSeed(): int{
        return $this->xpSeed;
    }
}