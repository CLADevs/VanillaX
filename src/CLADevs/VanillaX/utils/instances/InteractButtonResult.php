<?php

namespace CLADevs\VanillaX\utils\instances;

use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class InteractButtonResult{

    private Player $player;
    private ?Item $item;
    private ?Vector3 $clickPos;
    private ?string $button;
    private bool $interactQueue;

    public function __construct(Player $player, ?Item $item = null, ?string $button = null, ?Vector3 $clickPos = null, bool $interactQueue = true){
        $this->player = $player;
        $this->item = $item;
        $this->button = $button;
        $this->clickPos = $clickPos;
        $this->interactQueue = $interactQueue;
    }

    public function getPlayer(): Player{
        return $this->player;
    }

    public function getItem(): ?Item{
        return $this->item;
    }

    public function getClickPos(): ?Vector3{
        return $this->clickPos;
    }

    public function getButton(): ?string{
        return $this->button;
    }

    public function canInteractQueue(): bool{
        return $this->interactQueue;
    }

    public function setInteractQueue(bool $interactQueue): void{
        $this->interactQueue = $interactQueue;
    }
}