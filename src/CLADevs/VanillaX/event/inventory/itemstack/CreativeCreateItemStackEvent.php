<?php

namespace CLADevs\VanillaX\event\inventory\itemstack;

use pocketmine\item\Item;
use pocketmine\player\Player;

class CreativeCreateItemStackEvent extends ItemStackRequestEvent{

    public function __construct(protected Player $player, private Item $item, private int $index){
        parent::__construct($this->player);
    }

    public function getIndex(): int{
        return $this->index;
    }

    public function setItem(Item $item): void{
        $this->item = $item;
    }

    public function getItem(): Item{
        return $this->item;
    }
}