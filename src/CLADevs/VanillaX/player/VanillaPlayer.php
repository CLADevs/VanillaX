<?php

namespace CLADevs\VanillaX\player;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\Random;

class VanillaPlayer extends Player{

    private Random $random;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->random = new Random();
    }

    public function setXpSeed(int $xpSeed): void{
        $this->xpSeed = $xpSeed;
    }

    public function getXpSeed(): int{
        return $this->xpSeed;
    }

    public function getRandom(): Random{
        return $this->random;
    }
}