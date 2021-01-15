<?php

namespace CLADevs\VanillaX\entities\utils;

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\Player;

class EntityInteractResult{

    private Player $player;
    private ?Item $item;
    private ?Entity $entity;

    public function __construct(Player $player, ?Item $item = null, ?Entity $entity = null){
        $this->player = $player;
        $this->item = $item;
        $this->entity = $entity;
    }

    public function getPlayer(): Player{
        return $this->player;
    }

    public function getItem(): ?Item{
        return $this->item;
    }

    public function setItem(?Item $item): void{
        $this->item = $item;
    }

    public function isItem(): bool{
        return $this->item !== null;
    }

    public function getEntity(): ?Entity{
        return $this->entity;
    }

    public function setEntity(?Entity $entity): void{
        $this->entity = $entity;
    }

    public function isEntity(): bool{
        return $this->entity !== null;
    }
}