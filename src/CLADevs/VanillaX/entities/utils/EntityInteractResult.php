<?php

namespace CLADevs\VanillaX\entities\utils;

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class EntityInteractResult{

    private Player $player;
    private ?Item $item;
    private ?Entity $entity;
    private ?Vector3 $clickPos;
    private ?string $pressedButton;

    public function __construct(Player $player, ?Item $item = null, ?Entity $entity = null, ?Vector3 $clickPos = null, ?string $pressedButton = null){
        $this->player = $player;
        $this->item = $item;
        $this->entity = $entity;
        $this->clickPos = $clickPos;
        $this->pressedButton = $pressedButton;
    }

    public function getPlayer(): Player{
        return $this->player;
    }

    public function getClickPos(): ?Vector3{
        return $this->clickPos;
    }

    public function getItem(): ?Item{
        return $this->item;
    }

    public function setItem(?Item $item): void{
        $this->item = $item;
    }

    public function canUseItem(): bool{
        return $this->item !== null;
    }

    public function getEntity(): ?Entity{
        return $this->entity;
    }

    public function setEntity(?Entity $entity): void{
        $this->entity = $entity;
    }

    public function canUseEntity(): bool{
        return $this->entity !== null;
    }

    public function getPressedButton(): ?string{
        return $this->pressedButton;
    }
}