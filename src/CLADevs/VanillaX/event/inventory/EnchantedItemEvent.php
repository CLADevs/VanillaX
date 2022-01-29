<?php

namespace CLADevs\VanillaX\event\inventory;

use pocketmine\event\Event;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\player\Player;

class EnchantedItemEvent extends Event{

    private Player $player;
    private Item $input;
    private Item $result;

    private int $cost;

    /** @var EnchantmentInstance[] */
    private array $enchantments;

    public function __construct(Player $player, Item $input, Item $result, int $cost){
        $this->player = $player;
        $this->input = $input;
        $this->cost = $cost;
        $this->result = $result;
        $this->enchantments = $result->getEnchantments();
    }

    public function getPlayer(): Player{
        return $this->player;
    }

    public function getInput(): Item{
        return $this->input;
    }

    public function getCost(): int{
        return $this->cost;
    }

    public function getResult(): Item{
        return $this->result;
    }

    /**
     * @return EnchantmentInstance[]
     */
    public function getEnchantments(): array{
        return $this->enchantments;
    }
}
