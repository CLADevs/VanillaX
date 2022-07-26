<?php

namespace CLADevs\VanillaX\event\inventory\itemstack;

use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\ItemStackRequestSlotInfo;
use pocketmine\player\Player;

class SwapItemStackEvent extends ItemStackRequestEvent{

    public function __construct(protected Player $player, private ItemStackRequestSlotInfo $source, private ItemStackRequestSlotInfo $destination){
        parent::__construct($this->player);
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
