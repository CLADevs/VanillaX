<?php

namespace CLADevs\VanillaX\event\inventory\itemstack;

use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\types\recipe\RecipeWithTypeId;
use pocketmine\player\Player;

class CraftItemStackEvent extends ItemStackRequestEvent{

    public function __construct(protected Player $player, private RecipeWithTypeId $recipe, private Item $result, private int $repetitions, private bool $auto){
        parent::__construct($this->player);
    }

    public function isAuto(): bool{
        return $this->auto;
    }

    public function getRecipe(): RecipeWithTypeId{
        return $this->recipe;
    }

    public function getRepetitions(): int{
        return $this->repetitions;
    }

    public function setResult(Item $result): void{
        $this->result = $result;
    }

    public function getResult(): Item{
        return $this->result;
    }
}