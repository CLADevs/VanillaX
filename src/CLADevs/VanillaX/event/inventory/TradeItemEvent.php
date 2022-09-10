<?php

namespace CLADevs\VanillaX\event\inventory;

use CLADevs\VanillaX\entities\passive\VillagerEntity;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;

class TradeItemEvent extends Event{

    public function __construct(private Player $player, private VillagerEntity $villager, private Item $buyA, private ?Item $buyB, private Item $sell, private int $experience){
    }

    public function getPlayer(): Player{
        return $this->player;
    }

    public function getVillager(): VillagerEntity{
        return $this->villager;
    }

    public function getBuyA(): Item{
        return $this->buyA;
    }

    public function getBuyB(): ?Item{
        return $this->buyB;
    }

    public function getSell(): Item{
        return $this->sell;
    }

    public function getExperience(): int{
        return $this->experience;
    }
}
