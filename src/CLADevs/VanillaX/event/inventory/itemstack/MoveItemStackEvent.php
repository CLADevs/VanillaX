<?php

namespace CLADevs\VanillaX\event\inventory\itemstack;

use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\ItemStackRequestSlotInfo;
use pocketmine\player\Player;

class MoveItemStackEvent extends ItemStackRequestEvent{

    const TYPE_TAKE = 0;
    const TYPE_PLACE = 1;

    public function __construct(protected Player $player, private int $type, private int $count, private ItemStackRequestSlotInfo $source, private ItemStackRequestSlotInfo $destination){
        parent::__construct($this->player);
    }

    public function getType(): int{
        return $this->type;
    }

    public function setCount(int $count): void{
        $this->count = $count;
    }

    public function getCount(): int{
        return $this->count;
    }

    public function setSource(ItemStackRequestSlotInfo $source): void{
        $this->source = $source;
    }

    public function getSource(): ItemStackRequestSlotInfo{
        return $this->source;
    }

    public function setDestination(ItemStackRequestSlotInfo $destination): void{
        $this->destination = $destination;
    }

    public function getDestination(): ItemStackRequestSlotInfo{
        return $this->destination;
    }
}
