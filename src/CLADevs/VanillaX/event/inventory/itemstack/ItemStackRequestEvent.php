<?php

namespace CLADevs\VanillaX\event\inventory\itemstack;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\player\Player;

abstract class ItemStackRequestEvent extends Event implements Cancellable{
    use CancellableTrait;

    public function __construct(protected Player $player){
    }

    public function getPlayer(): Player{
        return $this->player;
    }
}
