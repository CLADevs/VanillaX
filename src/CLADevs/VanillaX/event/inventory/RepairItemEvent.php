<?php

namespace CLADevs\VanillaX\event\inventory;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;

class RepairItemEvent extends Event implements Cancellable{
    use CancellableTrait;

    public function __construct(private Player $player, private Item $input, private ?Item $material, private Item $result, private int $cost){
    }

    /**
     * @return Player
     * VanillaPlayer who is trying to repair their item
     */
    public function getPlayer(): Player{
        return $this->player;
    }

    /**
     * @return Item
     * Item that is being repaired
     */
    public function getInput(): Item{
        return $this->input;
    }

    /**
     * @return Item|null
     * Material used to repair
     */
    public function getMaterial(): ?Item{
        return $this->material;
    }

    /**
     * @return Item
     * Result item once its repaired
     */
    public function getResult(): Item{
        return $this->result;
    }

    /**
     * @return int
     * Amount of experience level cost to repair
     */
    public function getCost(): int{
        return $this->cost;
    }
}
