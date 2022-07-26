<?php

namespace CLADevs\VanillaX\event\inventory\itemstack;

use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\ItemStackRequestSlotInfo;
use pocketmine\player\Player;

class DropItemStackEvent extends ItemStackRequestEvent{

    public function __construct(protected Player $player, private ItemStackRequestSlotInfo $source, private int $count, private bool $randomly){
        parent::__construct($this->player);
    }

    public function setSource(ItemStackRequestSlotInfo $source): void{
        $this->source = $source;
    }

    public function getSource(): ItemStackRequestSlotInfo{
        return $this->source;
    }

    public function setCount(int $count): void{
        $this->count = $count;
    }

    public function getCount(): int{
        return $this->count;
    }

    public function setRandomly(bool $randomly): void{
        $this->randomly = $randomly;
    }

    public function isRandomly(): bool{
        return $this->randomly;
    }
}
