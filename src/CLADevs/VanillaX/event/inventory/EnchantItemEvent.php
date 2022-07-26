<?php

namespace CLADevs\VanillaX\event\inventory;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\player\Player;

class EnchantItemEvent extends Event implements Cancellable{
    use CancellableTrait;

    /**
     * @param Player $player
     * @param Item $input
     * @param EnchantmentInstance[] $enchantments
     * @param int $levelCost
     * @param int $materialsCost
     */
    public function __construct(private Player $player, private Item $input, private array $enchantments, private int $levelCost, private int $materialsCost){
    }

    /**
     * @return Player
     * VanillaPlayer who is enchanting the item
     */
    public function getPlayer(): Player{
        return $this->player;
    }

    public function setInput(Item $input): void{
        $this->input = $input;
    }

    /**
     * @return Item
     * Item that is being enchanted
     */
    public function getInput(): Item{
        return $this->input;
    }

    /**
     * @param EnchantmentInstance[] $enchantments
     */
    public function setEnchantments(array $enchantments): void{
        $this->enchantments = $enchantments;
    }

    /**
     * @return EnchantmentInstance[]
     * Enchantments given by enchantment table
     */
    public function getEnchantments(): array{
        return $this->enchantments;
    }

    /**
     * @return int
     * amount of experience level it cost to enchant that item
     */
    public function getLevelCost(): int{
        return $this->levelCost;
    }

    public function setLevelCost(int $levelCost): void{
        $this->levelCost = $levelCost;
    }

    /**
     * @return int
     * amount of lapis it cost to enchant that
     */
    public function getMaterialsCost(): int{
        return $this->materialsCost;
    }

    public function setMaterialsCost(int $materialsCost): void{
        $this->materialsCost = $materialsCost;
    }
}
