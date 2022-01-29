<?php

namespace CLADevs\VanillaX\event\inventory;

use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;

class TradeItemEvent extends Event{

    private Player $player;
    private Item $input;
    private ?Item $input2;
    private Item $result;

    private int $experience;

    public function __construct(Player $player, Item $input, ?Item $input2, Item $result, int $experience){
        $this->player = $player;
        $this->input = $input;
        $this->input2 = $input2;
        $this->result = $result;
        $this->experience = $experience;
    }

    public function getPlayer(): Player{
        return $this->player;
    }

    public function getInput(): Item{
        return $this->input;
    }

    public function getInput2(): ?Item{
        return $this->input2;
    }

    public function getResult(): Item{
        return $this->result;
    }

    public function getExperience(): int{
        return $this->experience;
    }
}
