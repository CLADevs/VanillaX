<?php

namespace CLADevs\VanillaX\event\inventory;

use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;

class RepairedItemEvent extends Event{

    private Player $player;
    private Item $input;
    private ?Item $material;
    private Item $result;

    public function __construct(Player $player, Item $input, ?Item $material, Item $result){
        $this->player = $player;
        $this->input = $input;
        $this->material = $material;
        $this->result = $result;
    }

    public function getPlayer(): Player{
        return $this->player;
    }

    public function getInput(): Item{
        return $this->input;
    }

    public function getMaterial(): ?Item{
        return $this->material;
    }

    public function getResult(): Item{
        return $this->result;
    }
}
