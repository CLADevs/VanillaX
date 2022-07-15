<?php

namespace CLADevs\VanillaX\event\inventory;

use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;

class UpgradedItemEvent extends Event{
    use CancellableTrait;

    public function __construct(private Player $player, private Item $input, private Item $materialCost, private Item $result){
    }

    public function getPlayer(): Player{
        return $this->player;
    }

    public function getInput(): Item{
        return $this->input;
    }

    public function getMaterialCost(): Item{
        return $this->materialCost;
    }

    public function getResult(): Item{
        return $this->result;
    }
}
