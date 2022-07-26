<?php

namespace CLADevs\VanillaX\event\inventory;

use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\types\recipe\ShapelessRecipe;
use pocketmine\player\Player;

class UpgradeItemEvent extends Event{
    use CancellableTrait;

    public function __construct(private Player $player, private ShapelessRecipe $recipe, private Item $input, private Item $materialCost, private Item $result){
    }

    /**
     * @return Player
     * Whoever is using smithing table
     */
    public function getPlayer(): Player{
        return $this->player;
    }

    /**
     * @return ShapelessRecipe
     * Recipe for this smithing table
     */
    public function getRecipe(): ShapelessRecipe{
        return $this->recipe;
    }

    /**
     * @return Item
     * Item you put in to upgrade
     */
    public function getInput(): Item{
        return $this->input;
    }

    /**
     * @return Item
     * How much netherite ingot it cost
     */
    public function getMaterialCost(): Item{
        return $this->materialCost;
    }

    public function setResult(Item $result): void{
        $this->result = $result;
    }

    /**
     * @return Item
     * Outcome of that recipe
     */
    public function getResult(): Item{
        return $this->result;
    }
}
